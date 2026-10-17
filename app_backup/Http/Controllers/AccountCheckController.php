<?php

namespace App\Http\Controllers;

use App\Services\PublicAccountCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountCheckController extends Controller
{
    public function __construct(
        protected PublicAccountCheckService $accountCheckService
    ) {}

    public function index()
    {
        return view('cek-akun.index');
    }

    public function manual()
    {
        return view('cek-akun.manual');
    }

    public function qr()
    {
        return view('cek-akun.qr');
    }

    public function nfc()
    {
        return view('cek-akun.nfc');
    }

    public function resolve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'value'  => ['required', 'string', 'max:500'],
            'method' => ['required', Rule::in(['manual', 'barcode', 'nfc', 'upload'])],
        ]);

        $result = $this->accountCheckService->resolve(
            rawValue: $validated['value'],
            method: $validated['method'],
            context: [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        );

        $status = $result['http_status'] ?? ($result['ok'] ? 200 : 422);
        unset($result['http_status']);

        return response()
            ->json($result, $status)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}