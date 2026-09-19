<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with(['sender', 'user'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter status baca
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->paginate(15)->appends($request->query());
        $unreadCount = Notification::where('user_id', auth()->id())->unread()->count();

        // Data untuk filter
        $types = Notification::where('user_id', auth()->id())
            ->distinct('type')
            ->pluck('type')
            ->filter();

        return view('owner.notifications.index', compact('notifications', 'unreadCount', 'types'));
    }

    public function show(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        // Tandai sebagai dibaca
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return view('owner.notifications.show', compact('notification'));
    }

    public function read(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->link) {
            return redirect($notification->link);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai dibaca.');
    }

    public function readAll(Request $request)
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Semua notifikasi telah dibaca.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi dihapus.');
    }

    public function destroyAll()
    {
        Notification::where('user_id', auth()->id())->delete();

        return redirect()->back()->with('success', 'Semua notifikasi telah dihapus.');
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', auth()->id())->unread()->count();
        return response()->json(['count' => $count]);
    }
}