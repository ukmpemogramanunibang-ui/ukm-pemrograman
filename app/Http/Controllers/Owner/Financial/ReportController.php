<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\Invoice;
use App\Models\Financial\PaymentTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Statistik laporan
        $totalInvoices = Invoice::count();
        $totalPaid = Invoice::where('status', 'paid')->sum('total');
        $totalPending = Invoice::where('status', 'pending')->sum('total');
        $totalOverdue = Invoice::where('status', 'overdue')->sum('total');
        $totalPenalty = Invoice::sum('penalty');
        $totalPayments = PaymentTransaction::where('status', 'paid')->sum('amount');

        // Data per bulan (6 bulan terakhir)
        $monthlyData = Invoice::where('status', 'paid')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Data per kategori
        $categoryData = Invoice::where('status', 'paid')
            ->selectRaw('type, SUM(total) as total')
            ->groupBy('type')
            ->get();

        return view('owner.financial.reports.index', compact(
            'totalInvoices',
            'totalPaid',
            'totalPending',
            'totalOverdue',
            'totalPenalty',
            'totalPayments',
            'monthlyData',
            'categoryData'
        ));
    }

    public function export(Request $request)
    {
        // Placeholder untuk export laporan
        return redirect()->back()->with('info', 'Fitur export laporan sedang dalam pengembangan.');
    }
}