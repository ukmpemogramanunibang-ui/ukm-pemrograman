<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Notification;
use Carbon\Carbon;

class SecretaryController extends Controller
{
    public function dashboard()
    {
        $totalMembers = User::where('level_id', 5)->count();
        $totalOfficials = User::whereIn('level_id', [3,4])->count();
        $totalRooms = Room::count();
        $totalBookingsToday = Booking::whereDate('start_datetime', today())->count();

        $pendingCount = Booking::where('status', 'Menunggu Persetujuan')->count();

        $upcomingEvents = Event::where('start_date', '>', now())
            ->where('status', 'active')
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $totalEvents = Event::where('status', 'active')->count();

        $unreadNotifications = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        $pendingRequests = Booking::where('requester_user_id', auth()->id())
            ->where('status', 'Menunggu Persetujuan')
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime')
            ->take(5)
            ->get();

        $recentEvents = Event::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $tasksCount = 12;
        $newDocuments = 5;

        $upcomingTasks = collect([
            (object) ['title' => 'Membuat surat kegiatan', 'deadline' => now()->addDay()],
            (object) ['title' => 'Notulen rapat', 'deadline' => Carbon::parse('2026-08-29')],
            (object) ['title' => 'Upload laporan', 'deadline' => Carbon::parse('2026-08-30')],
        ]);

        $latestAnnouncements = collect();

        return view('owner.officials.secretary', compact(
            'totalMembers',
            'totalOfficials',
            'totalRooms',
            'totalBookingsToday',
            'pendingCount',
            'upcomingEvents',
            'totalEvents',
            'unreadNotifications',
            'pendingRequests',
            'recentEvents',
            'tasksCount',
            'newDocuments',
            'upcomingTasks',
            'latestAnnouncements'
        ));
    }
}