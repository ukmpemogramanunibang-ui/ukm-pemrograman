<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Services\Financial\MemberFinancialService;
use Illuminate\Http\Request;

class UangkasController extends Controller
{
    public function __construct(
        protected MemberFinancialService $finance
    ) {}

public function index(Request $request)
{
    $user = $request->user();

    // Prioritas: cek dulu apakah view spesifik level tersedia
    $view = match ((int) $user->level_id) {
        5       => 'anggota-ukm.uangkas',
        6       => 'anggota-umum.uangkas',
        default => 'anggota-umum.uangkas',
    };

    if (!view()->exists($view)) {
        $view = 'anggota-umum.uangkas';
    }

    if (!view()->exists($view)) {
        $view = 'anggota-ukm.uangkas';
    }

    return view($view, [
        'user'    => $user,
        'summary' => $this->finance->summaryFor($user),
    ]);
}
}