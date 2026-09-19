<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class ChatController extends Controller
{
    /**
     * Tampilkan daftar percakapan privat Owner.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua percakapan yang diikuti user
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->where('type', 'private')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('owner.chats.index', compact('conversations'));
    }

    /**
     * Tampilkan chat privat dengan user tertentu.
     */
    public function private($uuid = null)
    {
        $user = Auth::user();

        // Jika ada UUID, cari user tujuan
        $chatUser = null;
        $conversation = null;
        $messages = collect();

        if ($uuid) {
            $chatUser = User::where('user_uuid', $uuid)->firstOrFail();

            // Cari atau buat percakapan privat
            $conversation = Conversation::private($user->id, $chatUser->id)->first();

            if (!$conversation) {
                // Buat percakapan baru
                $conversation = DB::transaction(function () use ($user, $chatUser) {
                    $conv = Conversation::create([
                        'type' => 'private',
                        'created_by' => $user->id,
                        'last_message_at' => now(),
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id' => $user->id,
                        'role' => 'admin',
                        'joined_at' => now(),
                    ]);

                    ConversationParticipant::create([
                        'conversation_id' => $conv->id,
                        'user_id' => $chatUser->id,
                        'role' => 'member',
                        'joined_at' => now(),
                    ]);

                    return $conv;
                });
            }

            // Ambil pesan
            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();

            // Tandai pesan sebagai read
            Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $user->id)
                ->where('status', '!=', 'read')
                ->update([
                    'status' => 'read',
                    'read_at' => now(),
                ]);

            // Update last_read_at
            $participant = $conversation->participants()->where('user_id', $user->id)->first();
            if ($participant) {
                $participant->update(['last_read_at' => now()]);
            }
        }

        // Ambil daftar percakapan untuk sidebar
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->where('type', 'private')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('owner.chats.private', compact(
            'conversations',
            'chatUser',
            'conversation',
            'messages'
        ));
    }

    /**
     * Kirim pesan (AJAX).
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        // Cek apakah user adalah peserta
        $participant = $conversation->participants()->where('user_id', $user->id)->first();
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan peserta percakapan ini.'
            ], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'status' => 'sent',
        ]);

        // Update last_message_at
        $conversation->update(['last_message_at' => now()]);
// --- MULAI PERBAIKAN NOTIFIKASI ---
// Ambil penerima (peserta lain di percakapan ini)
$receiver = $conversation->participants()
    ->where('user_id', '!=', $user->id)
    ->first()?->user;

// Kirim notifikasi jika penerima ditemukan
if ($receiver) {
    Notification::create([
        'user_id' => $receiver->id,
        'type'    => 'info',
        'title'   => '💬 Pesan Chat Baru',
        'message' => "{$user->name} mengirim pesan kepada Anda.",
        'icon'    => 'fas fa-comments',
        // Arahkan ke chat dengan pengirim (lawannya)
        'link'    => route('owner.chats.private', $user->user_uuid), 
        'data'    => json_encode([
            'sender_id' => $user->id,
            'conversation_id' => $conversation->id,
            'message_preview' => \Str::limit($request->message, 30)
        ]),
    ]);
}
// --- SELESAI PERBAIKAN NOTIFIKASI ---

// Load sender
$message->load('sender');

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Ambil pesan baru (polling).
     */
    public function getNewMessages(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'last_id' => 'nullable|integer',
        ]);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        $query = Message::where('conversation_id', $conversation->id)
            ->with('sender');

        if ($request->last_id) {
            $query->where('id', '>', $request->last_id);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        // Tandai sebagai read (pesan yang dikirim orang lain)
        $messageIds = $messages->where('sender_id', '!=', $user->id)->pluck('id');
        if ($messageIds->isNotEmpty()) {
            Message::whereIn('id', $messageIds)->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Cari user untuk chat (AJAX).
     */
    public function searchUsers(Request $request)
    {
        $search = $request->input('q');
        $users = User::where('name', 'LIKE', "%{$search}%")
            ->orWhere('nim', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->where('id', '!=', Auth::id())
            ->limit(10)
            ->get(['id', 'user_uuid', 'name', 'nim', 'avatar_url']);

        return response()->json($users);
    }
}