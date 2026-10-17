<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(
        protected NotificationService $notifier
    ) {}

    /* ==========================================================
     | LIST PERCAKAPAN
     * ========================================================== */

    public function index()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->where('type', 'private')
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('owner.chats.index', compact('conversations'));
    }

    /* ==========================================================
     | CHAT PRIVAT
     * ========================================================== */

    public function private($uuid = null)
    {
        $user = Auth::user();

        $chatUser     = null;
        $conversation = null;
        $messages     = collect();

        if ($uuid) {
            $chatUser = User::where('user_uuid', $uuid)->firstOrFail();

            // Anti self-chat
            if ($chatUser->id === $user->id) {
                abort(403, 'Tidak dapat memulai chat dengan diri sendiri.');
            }

            $conversation = $this->findOrCreateConversation($user, $chatUser);

            if ($conversation) {
                $messages = $conversation->messages()
                    ->with(['sender:id,user_uuid,name,avatar_url'])
                    ->orderBy('id', 'asc')
                    ->limit(200)
                    ->get();

                // Tandai pesan sebagai read
                Message::where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('status', '!=', 'read')
                    ->update([
                        'status'  => 'read',
                        'read_at' => now(),
                    ]);

                // Update last_read_at
                ConversationParticipant::where('conversation_id', $conversation->id)
                    ->where('user_id', $user->id)
                    ->update(['last_read_at' => now()]);
            }
        }

        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants.user'])
            ->where('type', 'private')
            ->orderByDesc('last_message_at')
            ->limit(30)
            ->get();

        return view('owner.chats.private', compact(
            'conversations', 'chatUser', 'conversation', 'messages'
        ));
    }

    /* ==========================================================
     | KIRIM PESAN
     * ========================================================== */

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'message'         => ['required', 'string', 'max:1000'],
        ]);

        $user         = Auth::user();
        $conversation = Conversation::findOrFail($data['conversation_id']);

        // Cek peserta
        $isParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isParticipant) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan peserta percakapan ini.',
            ], 403);
        }

        try {
            $message = DB::transaction(function () use ($conversation, $user, $data) {
                $msg = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $user->id,
                    'message'         => $data['message'],
                    'status'          => 'sent',
                ]);

                $conversation->update(['last_message_at' => now()]);

                // Kirim notifikasi ke penerima
                $this->notifyChatReceiver($conversation, $user, $data['message']);

                return $msg;
            });

            $message->load('sender:id,user_uuid,name,avatar_url');

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Throwable $e) {
            \Log::warning('Chat send failed', [
                'user_id' => $user->id,
                'conv_id' => $conversation->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan. Silakan coba lagi.',
            ], 500);
        }
    }

    /* ==========================================================
     | POLLING — efisien, tidak loop
     * ========================================================== */

    public function getNewMessages(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'last_id'         => ['nullable', 'integer', 'min:0'],
        ]);

        $user         = Auth::user();
        $conversation = Conversation::findOrFail($data['conversation_id']);

        // Cek peserta
        $isParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isParticipant) {
            return response()->json(['success' => false], 403);
        }

        // ✅ Query efisien: pakai id cursor + limit
        $query = Message::where('conversation_id', $conversation->id)
            ->with(['sender:id,user_uuid,name,avatar_url'])
            ->orderBy('id', 'asc')
            ->limit(50);

        if (! empty($data['last_id'])) {
            $query->where('id', '>', $data['last_id']);
        }

        $messages = $query->get();

        // Tandai read (bulk, 1 query)
        $messageIds = $messages->where('sender_id', '!=', $user->id)->pluck('id');

        if ($messageIds->isNotEmpty()) {
            Message::whereIn('id', $messageIds)->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'success'  => true,
            'messages' => $messages,
            'count'    => $messages->count(),
        ]);
    }

    /* ==========================================================
     | SEARCH USER
     * ========================================================== */

    public function searchUsers(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('nim',  'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'user_uuid', 'name', 'nim', 'avatar_url']);

        return response()->json($users);
    }

    /* ==========================================================
     | INTERNAL HELPERS
     * ========================================================== */

    /**
     * Cari atau buat percakapan privat — race-condition safe.
     */
    protected function findOrCreateConversation(User $user, User $other): Conversation
    {
        $existing = Conversation::query()
            ->where('type', 'private')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $other->id))
            ->first();

        if ($existing) return $existing;

        return DB::transaction(function () use ($user, $other) {
            // Re-check di dalam transaction (race-condition safe)
            $existing = Conversation::query()
                ->where('type', 'private')
                ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn ($q) => $q->where('user_id', $other->id))
                ->lockForUpdate()
                ->first();

            if ($existing) return $existing;

            $conv = Conversation::create([
                'type'            => 'private',
                'created_by'      => $user->id,
                'last_message_at' => now(),
            ]);

            ConversationParticipant::insert([
                [
                    'conversation_id' => $conv->id,
                    'user_id'         => $user->id,
                    'role'            => 'admin',
                    'joined_at'       => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ],
                [
                    'conversation_id' => $conv->id,
                    'user_id'         => $other->id,
                    'role'            => 'member',
                    'joined_at'       => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ],
            ]);

            return $conv;
        });
    }

    /**
     * Kirim notifikasi ke penerima chat.
     */
    protected function notifyChatReceiver(Conversation $conversation, User $sender, string $messageText): void
    {
        // Ambil semua peserta lain (bisa >1 kalau grup, tapi private = 1)
        $receivers = $conversation->participants()
            ->where('user_id', '!=', $sender->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        foreach ($receivers as $receiver) {
            if (! $receiver instanceof User) continue;

            // Dedup: 1 notif per chat dalam 5 menit terakhir
            $dedupKey = 'chat:' . $conversation->id . ':user:' . $receiver->id;

            $this->notifier->sendToUser(
                $receiver,
                'Pesan Baru dari ' . $sender->name,
                Str::limit($messageText, 80),
                'chat',
                [
                    'icon'        => 'comments',
                    'priority'    => 'normal',
                    'action_url'  => route('owner.chats.private', $sender->user_uuid),
                    'actor_id'    => $sender->id,
                    'entity_type' => Conversation::class,
                    'entity_id'   => $conversation->id,
                    'dedup_key'   => $dedupKey . ':' . now()->format('YmdHi'),  // 1 notif per menit
                    'data'        => [
                        'title'           => 'Pesan Baru dari ' . $sender->name,
                        'message'         => Str::limit($messageText, 120),
                        'sender_id'       => $sender->id,
                        'sender_name'     => $sender->name,
                        'sender_uuid'     => $sender->user_uuid,
                        'conversation_id' => $conversation->id,
                        'message_preview' => Str::limit($messageText, 60),
                    ],
                ]
            );
        }
    }
}