<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Financial\Invoice;
use App\Models\Financial\PaymentTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalInvoices = Invoice::count();
        $pendingInvoices = Invoice::where('status', 'pending')->count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        $totalPayments = PaymentTransaction::sum('amount');
        $totalPenalty = Invoice::sum('penalty');

        $totalMembers = User::where('level_id', 5)->count();
        $totalOfficials = User::where('level_id', 4)->count();

        $monthlyData = Invoice::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return view('owner.financial.dashboard', compact(
            'totalInvoices',
            'pendingInvoices',
            'paidInvoices',
            'overdueInvoices',
            'totalPayments',
            'totalPenalty',
            'totalMembers',
            'totalOfficials',
            'monthlyData'
        ));
    }
}