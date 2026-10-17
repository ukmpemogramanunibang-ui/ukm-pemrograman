<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Minute;
use App\Models\MinuteAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class MinutesController extends Controller
{
    public function index(Request $request)
    {
        $query = Minute::with(['notulis', 'creator'])
            ->where('created_by', auth()->id())
            ->orWhere('notulis_id', auth()->id());

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter bulan
        if ($request->filled('month')) {
            $query->whereMonth('meeting_date', $request->month);
        }

        // Filter tahun
        if ($request->filled('year')) {
            $query->whereYear('meeting_date', $request->year);
        }

        $minutes = $query->orderBy('meeting_date', 'desc')->paginate(15);

        // Statistik
        $totalMinutes = Minute::where('created_by', auth()->id())->count();
        $draftMinutes = Minute::where('created_by', auth()->id())->draft()->count();
        $completedMinutes = Minute::where('created_by', auth()->id())->completed()->count();
        $archivedMinutes = Minute::where('created_by', auth()->id())->archived()->count();

        // Tahun untuk filter
        $years = Minute::selectRaw('YEAR(meeting_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('secretary.minutes.index', compact(
            'minutes',
            'totalMinutes',
            'draftMinutes',
            'completedMinutes',
            'archivedMinutes',
            'years'
        ));
    }

public function create()
{
    $users = \App\Models\User::whereIn('level_id', [1, 2, 3, 4])->get();

    // Ambil booking yang sudah disetujui dan belum lewat
    $bookings = \App\Models\Booking::with(['room', 'user'])
        ->where('status', 'Disetujui')
        ->where('start_datetime', '>', now())
        ->orderBy('start_datetime', 'asc')
        ->get();

    return view('secretary.minutes.create', compact('users', 'bookings'));
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'leader_name' => 'nullable|string|max:255',
            'participants' => 'nullable|string',
            'agenda' => 'nullable|string',
            'discussion' => 'nullable|string',
            'decisions' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'status' => 'required|in:draft,completed',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
        ]);

        $data = $request->except(['attachments', '_token']);
        $data['notulis_id'] = auth()->id();
        $data['created_by'] = auth()->id();

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        $minute = Minute::create($data);

        // Upload lampiran
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('minutes/' . $minute->id, $filename, 'public');

                MinuteAttachment::create([
                    'minute_id' => $minute->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('owner.officials.secretary.minutes.show', $minute->id)
            ->with('success', 'Notulen berhasil disimpan.');
    }

    public function show(Minute $minute)
    {
        $this->authorizeAccess($minute);
        $minute->load(['notulis', 'creator', 'attachments']);

        return view('secretary.minutes.show', compact('minute'));
    }

    public function edit(Minute $minute)
    {
        $this->authorizeAccess($minute);
        $users = \App\Models\User::whereIn('level_id', [1, 2, 3, 4])->get();

        return view('secretary.minutes.edit', compact('minute', 'users'));
    }

    public function update(Request $request, Minute $minute)
    {
        $this->authorizeAccess($minute);

        $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'leader_name' => 'nullable|string|max:255',
            'participants' => 'nullable|string',
            'agenda' => 'nullable|string',
            'discussion' => 'nullable|string',
            'decisions' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'status' => 'required|in:draft,completed',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
        ]);

        $data = $request->except(['attachments', '_token', '_method']);
        $data['updated_by'] = auth()->id();

        if ($request->status === 'completed' && $minute->status !== 'completed') {
            $data['completed_at'] = now();
        }

        $minute->update($data);

        // Upload lampiran baru
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('minutes/' . $minute->id, $filename, 'public');

                MinuteAttachment::create([
                    'minute_id' => $minute->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('owner.officials.secretary.minutes.show', $minute->id)
            ->with('success', 'Notulen berhasil diperbarui.');
    }

    public function destroy(Minute $minute)
    {
        $this->authorizeAccess($minute);

        // Hapus lampiran dari storage
        foreach ($minute->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $minute->delete();

        return redirect()->route('owner.officials.secretary.minutes.index')
            ->with('success', 'Notulen berhasil dihapus.');
    }

    // === Fitur Tambahan ===

    public function archive(Minute $minute)
    {
        $this->authorizeAccess($minute);

        if ($minute->status === 'draft') {
            return redirect()->back()->with('error', 'Notulen draft tidak dapat diarsipkan. Selesaikan terlebih dahulu.');
        }

        $minute->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Notulen berhasil diarsipkan.');
    }

    public function unarchive(Minute $minute)
    {
        $this->authorizeAccess($minute);

        $minute->update([
            'status' => 'completed',
            'archived_at' => null,
        ]);

        return redirect()->back()->with('success', 'Notulen dikembalikan dari arsip.');
    }

    public function exportPdf(Minute $minute)
    {
        $this->authorizeAccess($minute);
        $minute->load(['notulis', 'creator', 'attachments']);

        $pdf = PDF::loadView('secretary.minutes.pdf', compact('minute'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Notulen_' . Str::slug($minute->title) . '_' . $minute->meeting_date->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadAttachment(MinuteAttachment $attachment)
    {
        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function deleteAttachment(MinuteAttachment $attachment)
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $attachment->delete();

        return redirect()->back()->with('success', 'Lampiran berhasil dihapus.');
    }

    // === Helper ===
    private function authorizeAccess(Minute $minute): void
    {
        if ($minute->created_by !== auth()->id() && $minute->notulis_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke notulen ini.');
        }
    }
    public function getBookingData(Booking $booking)
{
    $room = $booking->room;

    return response()->json([
        'success' => true,
        'data' => [
            'title' => $booking->title,
            'meeting_date' => $booking->start_datetime->format('Y-m-d'),
            'meeting_time' => $booking->start_datetime->format('H:i'),
            'end_time' => $booking->end_datetime->format('H:i'),
            'location' => $room->name . ' - ' . ($room->building ?? '') . ($room->floor ? ' Lt.' . $room->floor : ''),
            'room_id' => $room->id,
            'room_name' => $room->name,
            'room_capacity' => $room->capacity_normal,
            'room_facilities' => $room->facilities->pluck('name')->implode(', '),
            'booking_id' => $booking->id,
            'requester_name' => $booking->requester_name,
            'participants' => $booking->participant_count ?? 0,
            'participants_list' => $booking->participants ?? '',
        ]
    ]);
}
}