<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        try {
            Log::info('Midtrans callback received:', $request->all());

            $serverKey = config('midtrans.server_key');
            $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            if ($hashed !== $request->signature_key) {
                Log::warning('Invalid signature for order_id: ' . $request->order_id);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // Proses notifikasi berdasarkan transaction_status
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                // TODO: Update status pesanan di database kamu
                // Contoh: Order::where('order_id', $request->order_id)->update(['status' => 'paid']);
            }

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}