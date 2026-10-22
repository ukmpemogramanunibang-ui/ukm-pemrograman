<?php

namespace App\Http\Controllers\Pengurus;

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

        return view('pengurus.keuangan.uangkas', [
            'user'    => $user,
            'summary' => $this->finance->summaryFor($user),
        ]);
    }
}