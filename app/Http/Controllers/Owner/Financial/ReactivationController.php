<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\ReactivationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ReactivationController extends Controller
{
    public function index()
    {
        $requests = ReactivationRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('owner.financial.reactivations.index', compact('requests'));
    }

    public function approve($id)
    {
        $request = ReactivationRequest::findOrFail($id);
        $request->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update status user
        $user = $request->user;
        if ($user) {
            $user->membership_status = 'active';
            $user->suspended_at = null;
            $user->blacklisted_at = null;
            $user->save();
        }

        return redirect()->route('owner.financial.reactivations.index')
            ->with('success', 'Permintaan reaktivasi disetujui.');
    }

    public function reject($id)
    {
        $request = ReactivationRequest::findOrFail($id);
        $request->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('owner.financial.reactivations.index')
            ->with('error', 'Permintaan reaktivasi ditolak.');
    }
}