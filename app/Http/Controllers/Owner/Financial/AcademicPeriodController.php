<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicPeriodController extends Controller
{
    /**
     * Display a listing of academic periods.
     */
    public function index(Request $request)
    {
        $query = AcademicPeriod::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('semester', 'LIKE', "%{$search}%")
                  ->orWhere('year', 'LIKE', "%{$search}%");
            });
        }

        // Filter status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Order by terbaru
        $periods = $query->orderBy('year', 'desc')
                         ->orderBy('semester', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('owner.financial.academic-periods.index', compact('periods'));
    }

    /**
     * Show the form for creating a new academic period.
     */
    public function create()
    {
        // Ambil tahun unik untuk dropdown (opsional)
        $years = AcademicPeriod::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        return view('owner.financial.academic-periods.create', compact('years'));
    }

    /**
     * Store a newly created academic period in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'semester'      => 'required|string|max:50|in:Ganjil,Genap,Pendek',
            'year'          => 'required|string|max:9', // misal "2024/2025"
            'start_date'    => 'required|date|before:end_date',
            'end_date'      => 'required|date|after:start_date',
            'description'   => 'nullable|string|max:1000',
            'is_active'     => 'sometimes|boolean',
        ]);

        // Jika is_active tidak dikirim, set false
        $validated['is_active'] = $request->boolean('is_active', false);

        // Jika ada periode aktif lain dan kita set aktif, nonaktifkan yang lain (opsional)
        if ($validated['is_active']) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $period = AcademicPeriod::create($validated);

        return redirect()->route('owner.financial.academic-periods.index')
                         ->with('success', "Periode akademik '{$period->name}' berhasil ditambahkan.");
    }

    /**
     * Display the specified academic period.
     */
    public function show(string $id)
    {
        $period = AcademicPeriod::withTrashed()->findOrFail($id);
        return view('owner.financial.academic-periods.show', compact('period'));
    }

    /**
     * Show the form for editing the specified academic period.
     */
    public function edit(string $id)
    {
        $period = AcademicPeriod::findOrFail($id);
        $years = AcademicPeriod::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        return view('owner.financial.academic-periods.edit', compact('period', 'years'));
    }

    /**
     * Update the specified academic period in storage.
     */
    public function update(Request $request, string $id)
    {
        $period = AcademicPeriod::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'semester'      => 'required|string|max:50|in:Ganjil,Genap,Pendek',
            'year'          => 'required|string|max:9',
            'start_date'    => 'required|date|before:end_date',
            'end_date'      => 'required|date|after:start_date',
            'description'   => 'nullable|string|max:1000',
            'is_active'     => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);

        // Jika mengaktifkan, nonaktifkan yang lain
        if ($validated['is_active'] && !$period->is_active) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $period->update($validated);

        return redirect()->route('owner.financial.academic-periods.index')
                         ->with('success', "Periode akademik '{$period->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified academic period (soft delete).
     */
    public function destroy(string $id)
    {
        $period = AcademicPeriod::findOrFail($id);

        // Cegah penghapusan periode yang sedang aktif
        if ($period->is_active) {
            return redirect()->route('owner.financial.academic-periods.index')
                             ->with('error', 'Tidak dapat menghapus periode yang sedang aktif. Nonaktifkan terlebih dahulu.');
        }

        // Cek apakah periode memiliki relasi (misal generation, fee plan) - opsional
        // if ($period->generations()->exists() || $period->feePlans()->exists()) {
        //     return back()->with('error', 'Periode ini sudah memiliki data terkait (generasi/skema biaya). Hapus relasi terlebih dahulu.');
        // }

        $period->delete();

        return redirect()->route('owner.financial.academic-periods.index')
                         ->with('success', "Periode akademik '{$period->name}' berhasil dihapus.");
    }

    /**
     * (Opsional) Aktifkan/nonaktifkan periode secara cepat.
     * Jika route tidak ada, kita bisa tambahkan sendiri, tetapi karena resource
     * standar tidak menyediakan, kita bisa menambahkan method toggle jika diperlukan.
     * Untuk sementara kita lewati.
     */
}