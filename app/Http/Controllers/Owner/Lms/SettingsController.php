<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'max_attachments'    => 5,
            'max_attachment_size'=> 100, // MB
            'default_timezone'   => 'Asia/Jakarta',
            'auto_reminder_days' => [3, 1],
            'late_submission'    => true,
        ];

        return view('owner.lms.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'default_timezone'   => ['required', 'timezone'],
            'max_attachments'    => ['required', 'integer', 'min:1', 'max:10'],
            'max_attachment_size'=> ['required', 'integer', 'min:10', 'max:500'],
        ]);

        // Simpan ke config / DB setting (disesuaikan dengan sistem kamu)
        return back()->with('success', 'Pengaturan LMS diperbarui.');
    }

    public function notifications()
    {
        $user = auth()->user();

        $notifications = \DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('type', 'like', 'lms_%')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('owner.lms.notifications', compact('notifications'));
    }
}