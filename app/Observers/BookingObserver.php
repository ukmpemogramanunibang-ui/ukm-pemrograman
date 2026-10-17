<?php
namespace App\Observers;

use App\Models\Booking;
use App\Services\Notifications\NotificationService;

class BookingObserver
{
    public function __construct(protected NotificationService $svc) {}

    public function created(Booking $booking): void
    {
        $this->svc->syncEntity($booking, 'created', [
            'title'      => '📅 Booking Ruangan Baru',
            'message'    => $booking->user->name . ' mengajukan booking ' . $booking->ruangan->nama,
            'source'     => 'booking',
            'priority'   => 'normal',
            'action_url' => route('owner.bookings.show', $booking),
            'requires_action' => $booking->status === 'Menunggu Persetujuan',
            'status'     => $booking->status,
        ]);
    }

public function updated(Booking $booking): void
{
    // ✅ SEBELUM: skip jika TIDAK ada perubahan selain updated_at
    $dirtyKeys = array_keys($booking->getDirty());
    $meaningful = array_diff($dirtyKeys, ['updated_at', 'last_seen_at', 'view_count']);

    if (empty($meaningful)) return;

    $this->svc->syncEntity($booking, 'updated', [
        'title'           => '📅 Booking Ruangan (Diperbarui)',
        'message'         => $booking->user->name . ' mengubah booking ' . $booking->ruangan->nama,
        'source'          => 'booking',
        'priority'        => $booking->status === 'Dibatalkan' ? 'high' : 'normal',
        'action_url'      => route('owner.bookings.show', $booking),
        'status'          => $booking->status,
        'requires_action' => $booking->status === 'Menunggu Persetujuan',
    ]);
}

    public function deleted(Booking $booking): void
    {
        $this->svc->syncEntity($booking, 'deleted');
    }

    public function restored(Booking $booking): void
    {
        $this->svc->syncEntity($booking, 'restored', [
            'title'      => '📅 Booking Ruangan (Dipulihkan)',
            'message'    => 'Booking ' . $booking->ruangan->nama . ' dipulihkan.',
            'source'     => 'booking',
            'action_url' => route('owner.bookings.show', $booking),
        ]);
    }
}