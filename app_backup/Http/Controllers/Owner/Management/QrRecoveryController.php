<?php

namespace App\Http\Controllers\Owner\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQrCode;
use App\Services\QrRecoveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Zxing\QrReader; // <-- Tambahkan ini

class QrRecoveryController extends Controller
{
    protected QrRecoveryService $recoveryService;

    public function __construct(QrRecoveryService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    public function index()
    {
        return view('owner.management.qr.recovery');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file'            => 'required|file|max:10240|mimes:png,jpg,jpeg,webp,pdf',
            'decoded_payload' => 'required|string|max:5000',
        ]);

        // Verifikasi payload client
        $payload = $this->recoveryService->verifyQrPayload($request->decoded_payload);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak valid atau bukan QR resmi UKM Programming.',
            ], 422);
        }

        // Decode ulang di server untuk verifikasi tambahan (hanya untuk gambar)
        if (!in_array($request->file('file')->getMimeType(), ['application/pdf'])) {
            $serverDecoded = $this->decodeQrFromFile($request->file('file'));
            if ($serverDecoded && $serverDecoded !== $request->decoded_payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR dari client tidak cocok dengan hasil decode server.',
                ], 422);
            }
        }

        $found = $this->recoveryService->findUserFromPayload($payload);

        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'QR resmi, tetapi data pemilik tidak ditemukan di sistem maupun arsip pemulihan.',
            ], 404);
        }

        if ($found['found_in'] === 'database') {
            $user = $found['user'];
            $userData = [
                'id'            => $user->id,
                'user_uuid'     => $user->user_uuid,
                'name'          => $user->name,
                'nim'           => $user->nim,
                'member_number' => $user->member_number,
                'role'          => optional($user->level)->name,
                'is_active'     => $user->is_active,
                'is_verified'   => $user->is_verified,
                'avatar_url'    => $user->avatar_url,
            ];
            $recoveryStatus = 'Akun ditemukan di database';
        } else {
            $snapshot = $found['snapshot'];
            $userData = [
                'id'            => null,
                'user_uuid'     => $snapshot->user_uuid,
                'name'          => $snapshot->name,
                'nim'           => $snapshot->nim,
                'member_number' => $snapshot->member_number,
                'role'          => optional(\App\Models\Level::find($snapshot->level_id))->name ?? 'N/A',
                'is_active'     => true,
                'is_verified'   => false,
                'avatar_url'    => $snapshot->avatar_url,
            ];
            $recoveryStatus = 'Akun akan dipulihkan dari arsip';
        }

        return response()->json([
            'success'         => true,
            'user'            => $userData,
            'recovery_status' => $recoveryStatus,
            'qr_status'       => 'QR siap dipulihkan',
            'payload'         => $payload,
            'found_in'        => $found['found_in'],
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'files'             => 'required|array|min:1',
            'files.*'           => 'required|file|max:10240|mimes:png,jpg,jpeg,webp,pdf',
            'decoded_payloads'  => 'required|string', // JSON array
        ]);

        $decodedPayloads = json_decode($request->decoded_payloads, true);
        if (!is_array($decodedPayloads) || count($decodedPayloads) !== count($request->file('files'))) {
            return back()->withErrors(['files' => 'Jumlah payload tidak sesuai dengan jumlah file.']);
        }

        DB::beginTransaction();

        try {
            foreach ($request->file('files') as $index => $file) {
                $clientPayload = $decodedPayloads[$index] ?? null;
                if (!$clientPayload) {
                    throw new \RuntimeException('Payload QR tidak ditemukan untuk file ke-' . ($index + 1));
                }

                // --- VERIFIKASI SERVER-SIDE ---
                // 1. Decode ulang di server
                $serverDecoded = $this->decodeQrFromFile($file);
                if (!$serverDecoded) {
                    throw new \RuntimeException('Gagal membaca QR dari file ke-' . ($index + 1));
                }

                // 2. Bandingkan dengan payload client
                if ($serverDecoded !== $clientPayload) {
                    throw new \RuntimeException('QR dari client tidak cocok dengan hasil decode server untuk file ke-' . ($index + 1));
                }

                // 3. Verifikasi signature
                $payload = $this->recoveryService->verifyQrPayload($serverDecoded);
                if (!$payload) {
                    throw new \RuntimeException('QR tidak valid atau bukan QR resmi UKM Programming.');
                }

                // --- CARI USER / SNAPSHOT ---
                $found = $this->recoveryService->findUserFromPayload($payload);
                if (!$found) {
                    throw new \RuntimeException('Data pemilik QR tidak ditemukan.');
                }

                $user = null;
                if ($found['found_in'] === 'database') {
                    $user = $found['user'];
                    if ($user->trashed()) {
                        $user->restore();
                        $user->is_active = true;
                        $user->save();
                    }
                } else {
                    $snapshot = $found['snapshot'];
                    $user = $this->recoveryService->restoreUserFromSnapshot($snapshot);
                }

                // --- AKTIFKAN USER ---
                $user->is_active = true;
                $user->ukm_id = 1;
                $user->save();

                // --- NONAKTIFKAN QR LAMA ---
                UserQrCode::where('user_id', $user->id)->update([
                    'is_active'  => false,
                    'is_revoked' => true,
                    'revoked_at' => now(),
                ]);

                // --- BUAT QR BARU DARI PAYLOAD ---
                $qrUuid = $payload['qr_uuid'] ?? (string) Str::uuid();
                $token  = $payload['token'] ?? Str::random(120);

                $uploadedPath = null;
                if ($file->isValid()) {
                    $ext = $file->getClientOriginalExtension();
                    $filename = $qrUuid . '.' . $ext;
                    $uploadedPath = $file->storeAs('qr', $filename, 'public');
                }

                $qr = UserQrCode::updateOrCreate(
                    ['qr_uuid' => $qrUuid],
                    [
                        'token'      => $token,
                        'user_id'    => $user->id,
                        'qr_image'   => $uploadedPath,
                        'qr_path'    => $uploadedPath,
                        'is_active'  => true,
                        'is_revoked' => false,
                        'generated_at' => now(),
                        'revoked_at' => null,
                        'revoked_by' => null,
                    ]
                );

                $user->qr_token = $token;
                $user->save();

                // --- UPDATE SNAPSHOT ---
                $this->recoveryService->generateSnapshot($user);
            }

            DB::commit();

            return redirect()
                ->route('owner.management.qr.index')
                ->with('success', 'QR berhasil dipulihkan dan diaktifkan kembali.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('QR Recovery failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['files' => $e->getMessage()]);
        }
    }

    /**
     * Decode QR dari file (gambar atau PDF) menggunakan Zxing.
     * Untuk PDF, perlu render halaman terlebih dahulu (di luar scope).
     * Saat ini hanya mendukung gambar.
     */
    protected function decodeQrFromFile($file): ?string
    {
        // Jika PDF, kita tidak bisa decode langsung; bisa menggunakan library tambahan.
        // Untuk sekarang, kita tangani hanya gambar.
        if ($file->getMimeType() === 'application/pdf') {
            Log::warning('PDF QR decoding not implemented server-side, relying on client.');
            return null; // Atau bisa throw exception
        }

        try {
            $qrReader = new QrReader($file->path());
            $text = $qrReader->text();
            return $text ?: null;
        } catch (\Exception $e) {
            Log::error('QR decode error: ' . $e->getMessage());
            return null;
        }
    }
}