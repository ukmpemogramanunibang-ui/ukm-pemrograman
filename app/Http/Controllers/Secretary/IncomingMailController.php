<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\IncomingMail;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncomingMailController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomingMail::with(['pencatat', 'diteruskanKe']);

        // Filter
        if ($request->filled('search')) {
            $query->cari($request->search);
        }
        if ($request->filled('status')) {
            $query->status($request->status);
        }
        if ($request->filled('kategori')) {
            $query->kategori($request->kategori);
        }
        if ($request->filled('bulan')) {
            $query->bulan($request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->tahun($request->tahun);
        }

        $mails = $query->orderBy('tanggal_diterima', 'desc')->paginate(15);

        // Statistik
        $total = IncomingMail::count();
        $baru = IncomingMail::where('status', 'baru')->count();
        $diproses = IncomingMail::whereIn('status', ['dibaca', 'diproses', 'diteruskan'])->count();
        $diarsipkan = IncomingMail::where('status', 'diarsipkan')->count();

        // Data untuk filter
        $statuses = ['baru', 'dibaca', 'diproses', 'diteruskan', 'selesai', 'diarsipkan'];
        $kategoris = IncomingMail::distinct('kategori')->pluck('kategori');
        $bulans = range(1, 12);
        $tahuns = IncomingMail::selectRaw('YEAR(tanggal_diterima) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('secretary.incoming-mails.index', compact(
            'mails',
            'total',
            'baru',
            'diproses',
            'diarsipkan',
            'statuses',
            'kategoris',
            'bulans',
            'tahuns'
        ));
    }

    public function create()
    {
        $users = User::whereIn('level_id', [1, 2, 3, 4])->get(); // Owner, Superadmin, Ketua, Pengurus
        return view('secretary.incoming-mails.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|string|unique:incoming_mails,nomor_surat',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'tujuan' => 'nullable|string|max:255',
            'status' => 'required|in:baru,dibaca,diproses,diteruskan,selesai,diarsipkan',
            'lampiran' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except('lampiran');
        $data['dicatat_oleh'] = auth()->id();

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('incoming-mails', $filename, 'public');
            $data['lampiran'] = $path;
        }

        $mail = IncomingMail::create($data);

        return redirect()->route('owner.officials.secretary.incoming-mails.index')
            ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    public function show(IncomingMail $incomingMail)
    {
        return view('secretary.incoming-mails.show', compact('incomingMail'));
    }

    public function edit(IncomingMail $incomingMail)
    {
        $users = User::whereIn('level_id', [1, 2, 3, 4])->get();
        return view('secretary.incoming-mails.edit', compact('incomingMail', 'users'));
    }

    public function update(Request $request, IncomingMail $incomingMail)
    {
        $request->validate([
            'nomor_surat' => 'required|string|unique:incoming_mails,nomor_surat,' . $incomingMail->id,
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'tujuan' => 'nullable|string|max:255',
            'status' => 'required|in:baru,dibaca,diproses,diteruskan,selesai,diarsipkan',
            'lampiran' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,png',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except('lampiran');

        if ($request->hasFile('lampiran')) {
            // Hapus lampiran lama
            if ($incomingMail->lampiran && Storage::disk('public')->exists($incomingMail->lampiran)) {
                Storage::disk('public')->delete($incomingMail->lampiran);
            }
            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('incoming-mails', $filename, 'public');
            $data['lampiran'] = $path;
        }

        $incomingMail->update($data);

        return redirect()->route('owner.officials.secretary.incoming-mails.index')
            ->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(IncomingMail $incomingMail)
    {
        if ($incomingMail->lampiran && Storage::disk('public')->exists($incomingMail->lampiran)) {
            Storage::disk('public')->delete($incomingMail->lampiran);
        }
        $incomingMail->delete();

        return redirect()->route('owner.officials.secretary.incoming-mails.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }

    // Fitur Teruskan
    public function forward(Request $request, IncomingMail $incomingMail)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'catatan' => 'nullable|string',
        ]);

        $incomingMail->update([
            'status' => 'diteruskan',
            'diteruskan_kepada' => $request->user_id,
            'diteruskan_pada' => now(),
        ]);

        // Kirim notifikasi ke penerima
        Notification::create([
            'user_id' => $request->user_id,
            'type' => 'incoming_mail_forwarded',
            'title' => 'Surat Masuk Diteruskan',
            'message' => "Sekretaris meneruskan surat \"{$incomingMail->perihal}\" kepada Anda.",
            'data' => json_encode(['mail_id' => $incomingMail->id]),
            'read_at' => null,
        ]);

        return redirect()->back()->with('success', 'Surat berhasil diteruskan.');
    }

    // Fitur Arsipkan
    public function archive(IncomingMail $incomingMail)
{
    $incomingMail->update([
        'status' => 'diarsipkan',
        'diarsipkan_pada' => now(),
    ]);

    return redirect()->back()->with('success', 'Surat berhasil diarsipkan.');
}
}