<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = PaymentTransaction::with(['user', 'allocations.invoice'])->paginate(15);
        return view('owner.financial.payments.index', compact('payments'));
    }

    public function show(PaymentTransaction $payment)
    {
        $payment->load(['user', 'allocations.invoice']);
        return view('owner.financial.payments.show', compact('payment'));
    }
}