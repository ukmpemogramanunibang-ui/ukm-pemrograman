<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Financial\Invoice;   // ✅ Perbaiki ini
use App\Models\Announcement;        // Pastikan Announcement ada di sini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Financial\FeePlan;
use App\Services\Financial\FeePlanInvoiceGenerator;
use Carbon\Carbon;

class InvoiceController extends Controller
{
public function index()
{
    $invoices = Invoice::with(['user', 'feePlan.generation'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('owner.financial.invoices.index', compact('invoices'));
}

    /**
     * Detail tagihan
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'feePlan.generation', 'payments']);
        return view('owner.financial.invoices.show', compact('invoice'));
    }
 public function generateFromPlan(Request $request, FeePlan $feePlan)
    {
        $period = $request->input('period'); // optional: 2026-10

        $generator = app(FeePlanInvoiceGenerator::class);
        $preview   = $generator->preview($feePlan, $period);

        return view('owner.financial.invoices.generate-from-plan', [
            'plan'    => $feePlan,
            'preview' => $preview,
        ]);
    }

    /**
     * Eksekusi generate invoice.
     */
    public function storeFromPlan(Request $request, FeePlan $feePlan)
    {
        $period = $request->input('period');

        $generator = app(FeePlanInvoiceGenerator::class);
        $result    = $generator->generate($feePlan, $period);

        if ($result['created'] === 0) {
            return back()->with('warning', 'Tidak ada invoice baru yang dibuat. Mungkin semua user sudah ditagih untuk periode ini.');
        }

        return redirect()
            ->route('owner.financial.invoices.index')
            ->with('success', sprintf(
                '✅ Berhasil generate %d invoice untuk periode %s. Total: Rp %s. (%d user dilewati karena sudah ada invoice)',
                $result['created'],
                $result['period'],
                number_format($result['total_amount'], 0, ',', '.'),
                $result['skipped']
            ));
    }

    /**
     * Generate tagihan otomatis (contoh sederhana)
     */
    public function generate()
    {
        // Logika generate tagihan otomatis...
        // (sesuaikan dengan kebutuhan Anda)
        return redirect()->route('owner.financial.invoices.index')
            ->with('success', 'Tagihan berhasil digenerate!');
    }

    /**
     * Halaman cek valid tagihan akun
     * Menampilkan semua user (kecuali owner, superadmin, guest) dengan status tagihan
     */
    public function checkTagihan()
    {
        // Ambil user dengan role yang diizinkan (bukan owner, superadmin, guest)
        $users = User::whereHas('level', function ($query) {
            $query->whereNotIn('name', ['owner', 'superadmin', 'guest']);
        })
            ->with(['level', 'invoices' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->get();

        // Tentukan status tagihan
        $users->each(function ($user) {
            $user->tagihan_status = $user->invoices->count() > 0 ? 'sudah' : 'belum';
            // Tambahkan poin (misal dari relasi)
            $user->poin = $user->points_sum ?? 0;
        });

        return view('owner.financial.invoices.check-tagihan', compact('users'));
    }

    /**
     * Halaman cek valid pengumuman akun
     */
    public function checkPengumuman()
    {
        $users = User::whereHas('level', function ($query) {
            $query->whereNotIn('name', ['owner', 'superadmin', 'guest']);
        })
            ->with(['level', 'announcements' => function ($q) {
                $q->orderBy('announcement_user.created_at', 'desc');
            }])
            ->get();

        $users->each(function ($user) {
            $user->pengumuman_status = $user->announcements->count() > 0 ? 'sudah' : 'belum';
            $user->poin = $user->points_sum ?? 0;
        });

        return view('owner.financial.invoices.check-pengumuman', compact('users'));
    }

    /**
     * AJAX: Detail tagihan user (untuk modal preview)
     */
public function userTagihan(User $user)
{
    $invoices = $user->invoices()
        ->with(['feePlan.generation'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('owner.financial.invoices.partials._user_tagihan_modal', compact('user', 'invoices'));
}

    /**
     * AJAX: Detail pengumuman user (untuk modal preview)
     */
    public function userPengumuman(User $user)
{
    $announcements = $user->announcements()
        ->orderBy('announcement_user.created_at', 'desc')
        ->get();

    return view('owner.financial.invoices.partials._user_pengumuman_modal', compact('user', 'announcements'));
}
}