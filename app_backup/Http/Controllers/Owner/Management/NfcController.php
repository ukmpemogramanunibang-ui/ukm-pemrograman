<?php

namespace App\Http\Controllers\Owner\Management;

use App\Http\Controllers\Controller;
use App\Models\NfcCredential;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Zxing\QrReader;

class NfcController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | KONSTANTA
    |--------------------------------------------------------------------------
    */

    /** Cache TTL untuk stats (detik). */
    private const STATS_TTL = 300;

    /** Cache key untuk stats. */
    private const STATS_KEY = 'nfc.stats';

    /** Format UID fisik (hex 8-64 karakter). */
    private const UID_PATTERN = '/^[A-F0-9]{8,64}$/';

    /** Format UUID v4. */
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    /* ========================================================================
     | INDEX — daftar NFC
     | ===================================================================== */
    public function index(Request $request)
    {
        $query = User::with([
            'nfcCredential' => fn ($q) => $q->withTrashed(),
            'level',
        ]);

        // ----- Filter status -----
        switch ($request->input('status')) {
            case 'active':
                $query->whereHas('nfcCredential', fn ($q) => $q->active());
                break;
            case 'revoked':
                $query->whereHas('nfcCredential', fn ($q) => $q->where('is_revoked', true));
                break;
            case 'none':
                $query->whereDoesntHave('nfcCredential', fn ($q) => $q->active());
                break;
            case 'no_uid':
                // NFC aktif tapi belum punya UID fisik
                $query->whereHas('nfcCredential', fn ($q) => $q->active()->whereNull('nfc_identifier'));
                break;
        }

        // ----- Search -----
        if ($q = trim((string) $request->input('q'))) {
            $query->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$q}%")
                ->orWhere('nim', 'like', "%{$q}%")
            );
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        // ----- Stats -----
        $stats = $this->getCachedStats();

        // ----- Kandidat pindah NFC -----
        $moveCandidates = User::whereDoesntHave('nfcCredential', fn ($q) => $q->active())
            ->orderBy('name')
            ->get(['id', 'name', 'nim']);

        return view('owner.management.nfc.index', compact('users', 'stats', 'moveCandidates'));
    }

    /* ========================================================================
     | GENERATE — form
     | ===================================================================== */
    public function generate(Request $request)
    {
        // ----- Parse ?preselect=1,2,3 -----
        $requestedIds = $this->parseIds($request->input('preselect', ''));

        // ----- Ambil user + relasi -----
        $users = User::with([
                'nfcCredential' => fn ($q) => $q->withTrashed(),
                'level',
                'status',
                'jabatans',
            ])
            ->whereIn('id', $requestedIds)
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => $this->mapUserForGenerate($user));

        // ----- Pisahkan eligible vs blocked -----
        $eligibleUsers = $users->where('can_generate', true)->values();
        $blockedUsers  = $users->where('can_generate', false)->values();

        return view('owner.management.nfc.generate', [
            'users'        => $eligibleUsers,
            'blockedUsers' => $blockedUsers,
            'preselectIds' => $eligibleUsers->pluck('id')->values()->all(),
        ]);
    }

    /* ========================================================================
     | STORE — proses generate NFC
     | ===================================================================== */
    public function store(Request $request)
    {
        $request->validate([
            'user_ids'       => 'required|array|min:1',
            'user_ids.*'     => 'integer|exists:users,id',
            'nfc_identifier' => ['required', 'string', 'max:64', 'regex:' . self::UID_PATTERN],
            'expired_at'     => 'nullable|date|after:now',
            'replace'        => 'nullable|boolean',
        ], [
            'nfc_identifier.required' => 'UID Fisik kartu wajib diisi. Tap kartu NFC atau ketik manual.',
            'nfc_identifier.regex'    => 'UID harus hex (0-9, A-F), minimal 8 karakter.',
            'expired_at.after'        => 'Tanggal kedaluwarsa harus setelah sekarang.',
        ]);

        $userIds      = $request->input('user_ids');
        $identifierIn = strtoupper(trim((string) $request->input('nfc_identifier')));
        $replace      = $request->boolean('replace', false);
        $expiredAt    = $request->filled('expired_at')
            ? Carbon::parse($request->input('expired_at'))
            : null;

        // ----- Batch check: apakah UID sudah dipakai user lain? -----
        $existingOwner = NfcCredential::identifierOwnedByOther($identifierIn);
        if ($existingOwner && $existingOwner->user && count($userIds) > 1) {
            return back()->with('error', "❌ UID {$identifierIn} sudah dipakai {$existingOwner->user->name}. Tidak bisa assign ke banyak user sekaligus.");
        }

        $success  = 0;
        $restored = 0;
        $skipped  = [];
        $conflict = [];
        $failed   = [];
        $okNames  = [];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (! $user) {
                $skipped[] = "User #{$userId} tidak ditemukan";
                continue;
            }

            // ----- Cari credential TERMASUK soft-deleted -----
            $existing = NfcCredential::withTrashed()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->first();

            $hasActive = $existing && ! $existing->trashed() && $existing->isActive();

            // RULE 1: 1 user = 1 NFC aktif
            if ($hasActive && ! $replace) {
                $skipped[] = "{$user->name} sudah punya NFC aktif";
                continue;
            }

            // RULE 2: 1 kartu = 1 akun
            $owner = NfcCredential::identifierOwnedByOther($identifierIn, $user->id);
            if ($owner && $owner->user) {
                $conflict[] = "Kartu {$identifierIn} sudah dipakai {$owner->user->name} (NIM: {$owner->user->nim})";
                continue;
            }

            // ----- Eksekusi per-user dalam transaction -----
            try {
                $nfc = DB::transaction(function () use ($existing, $user, $identifierIn, $expiredAt, &$restored) {
                    if ($existing && $existing->trashed()) {
                        $existing->restore();
                        $restored++;
                    }

                    $payload = [
                        'nfc_uuid'       => (string) Str::uuid(),
                        'nfc_identifier' => $identifierIn,
                        'token'          => Str::random(64),
                        'status'         => NfcCredential::STATUS_ACTIVE,
                        'is_active'      => true,
                        'is_revoked'     => false,
                        'generated_at'   => now(),
                        'registered_at'  => now(),
                        'revoked_at'     => null,
                        'expired_at'     => $expiredAt,
                        'notes'          => null,
                        'nfc_image'      => null,
                    ];

                    if ($existing) {
                        $existing->update($payload);
                        return $existing->fresh();
                    }

                    return NfcCredential::create(array_merge($payload, [
                        'user_id' => $user->id,
                    ]));
                });

                // Defer QR generation
                $this->deferQrGeneration($nfc->id, $nfc->nfc_uuid);

                $this->audit('nfc.generate', [
                    'user_id'    => $user->id,
                    'nfc_uuid'   => $nfc->nfc_uuid,
                    'identifier' => $identifierIn,
                ]);

                $okNames[] = $user->name;
                $success++;
            } catch (\Throwable $e) {
                $failed[] = "{$user->name}: {$e->getMessage()}";
                Log::error('NFC generate failed', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        if ($success > 0) {
            $this->bumpStatsCache($success, $restored);
        }

        // ----- Build message -----
        $msg = "✅ Berhasil generate {$success} NFC.";
        if ($restored > 0) $msg .= " ♻️ {$restored} dipulihkan.";
        if ($skipped)      $msg .= ' ⚠️ ' . count($skipped) . ' dilewati.';
        if ($conflict)     $msg .= ' ❌ ' . count($conflict) . ' kartu ditolak.';
        if ($failed)       $msg .= ' 🛑 ' . count($failed) . ' gagal.';

        return redirect()
            ->route('owner.management.nfc.index')
            ->with('success', $msg)
            ->with('generated_users', $okNames ?: null)
            ->with('skipped',  $skipped ?: null)
            ->with('conflict', $conflict ?: null)
            ->with('failed',   $failed ?: null);
    }

    /*
|--------------------------------------------------------------------------
| ASSIGN IDENTIFIER — set/update UID fisik dari halaman testing
|--------------------------------------------------------------------------
*/
public function assignIdentifier(Request $request, NfcCredential $nfc)
{
    $request->validate([
        'nfc_identifier' => ['required', 'string', 'max:64', 'regex:' . self::UID_PATTERN],
    ], [
        'nfc_identifier.required' => 'UID Fisik wajib diisi.',
        'nfc_identifier.regex'    => 'UID harus hex (0-9, A-F), minimal 8 karakter.',
    ]);

    $identifier = strtoupper(trim($request->input('nfc_identifier')));

    // Cek apakah UID sudah dipakai user lain
    $owner = NfcCredential::identifierOwnedByOther($identifier, $nfc->user_id);
    if ($owner && $owner->user) {
        return response()->json([
            'success' => false,
            'message' => "UID {$identifier} sudah dipakai {$owner->user->name}.",
        ], 422);
    }

    $nfc->update(['nfc_identifier' => $identifier]);

    $this->audit('nfc.assign_identifier', [
        'nfc_uuid'   => $nfc->nfc_uuid,
        'identifier' => $identifier,
    ]);

    return response()->json([
        'success'    => true,
        'message'    => 'UID Fisik berhasil disimpan.',
        'identifier' => $identifier,
    ]);
}

    /* ========================================================================
     | BULK ASSIGN IDENTIFIER — set UID fisik untuk banyak NFC sekaligus
     | ===================================================================== */
    public function bulkAssignIdentifier(Request $request)
    {
        $request->validate([
            'assignments'              => 'required|array|min:1',
            'assignments.*.nfc_id'     => 'required|integer|exists:nfc_credentials,id',
            'assignments.*.identifier' => ['required', 'string', 'max:64', 'regex:' . self::UID_PATTERN],
        ], [
            'assignments.*.identifier.regex' => 'UID harus hex (0-9, A-F).',
        ]);

        $success = 0;
        $failed  = [];

        DB::transaction(function () use ($request, &$success, &$failed) {
            foreach ($request->input('assignments') as $row) {
                $nfc = NfcCredential::find($row['nfc_id']);
                if (! $nfc) {
                    $failed[] = "NFC #{$row['nfc_id']} tidak ditemukan";
                    continue;
                }

                $identifier = strtoupper(trim($row['identifier']));

                $owner = NfcCredential::identifierOwnedByOther($identifier, $nfc->user_id);
                if ($owner && $owner->user) {
                    $failed[] = "{$nfc->user->name}: UID {$identifier} sudah dipakai {$owner->user->name}";
                    continue;
                }

                $nfc->update(['nfc_identifier' => $identifier]);
                $success++;
            }
        });

        Cache::forget(self::STATS_KEY);

        $this->audit('nfc.bulk_assign_identifier', [
            'success' => $success,
            'failed'  => count($failed),
        ]);

        $msg = "✅ {$success} UID Fisik berhasil disimpan.";
        if ($failed) $msg .= ' ⚠️ ' . count($failed) . ' gagal.';

        return back()
            ->with('success', $msg)
            ->with('bulk_errors', $failed ?: null);
    }

    /* ========================================================================
     | MOVE — pindahkan credential ke user lain
     | ===================================================================== */
    public function move(Request $request, NfcCredential $nfc)
    {
        $request->validate([
            'new_user_id' => 'required|integer|exists:users,id',
        ]);

        if ((int) $nfc->user_id === (int) $request->new_user_id) {
            return back()->with('error', '❌ NFC sudah terhubung ke user tersebut.');
        }

        if ($nfc->trashed() || $nfc->is_revoked) {
            return back()->with('error', '❌ NFC sudah dicabut — tidak bisa dipindahkan.');
        }

        $newUser = User::findOrFail($request->new_user_id);

        $targetExisting = NfcCredential::withTrashed()
            ->where('user_id', $newUser->id)
            ->orderByDesc('id')
            ->first();

        if ($targetExisting && ! $targetExisting->trashed() && $targetExisting->isActive()) {
            return back()->with('error', "❌ {$newUser->name} sudah punya NFC aktif. Cabut NFC lamanya terlebih dahulu.");
        }

        $oldUser = $nfc->user;

        try {
            DB::transaction(function () use ($nfc, $targetExisting, $newUser) {
                if ($targetExisting && $targetExisting->id !== $nfc->id) {
                    $targetExisting->forceDelete();
                }

                $nfc->moveTo($newUser->id);
            });

            Cache::forget(self::STATS_KEY);
            $this->audit('nfc.move', [
                'nfc_uuid' => $nfc->nfc_uuid,
                'from'     => $oldUser?->id,
                'to'       => $newUser->id,
            ]);

            return back()->with(
                'success',
                "✅ NFC berhasil dipindahkan dari " . ($oldUser->name ?? 'user lama') . " ke {$newUser->name}."
            );
        } catch (\Throwable $e) {
            Log::error('NFC move failed', ['nfc' => $nfc->id, 'error' => $e->getMessage()]);
            return back()->with('error', '❌ Gagal memindahkan NFC: ' . $e->getMessage());
        }
    }

    /* ========================================================================
     | REVOKE — cabut credential
     | ===================================================================== */
    public function revoke(Request $request, NfcCredential $nfc)
    {
        if ($nfc->is_revoked) {
            return back()->with('error', '❌ NFC ini sudah dicabut sebelumnya.');
        }

        $reason = (string) $request->input('reason', 'Dicabut oleh admin');
        $nfc->revoke($reason);

        Cache::forget(self::STATS_KEY);
        $this->audit('nfc.revoke', [
            'nfc_uuid' => $nfc->nfc_uuid,
            'user_id'  => $nfc->user_id,
            'reason'   => $reason,
        ]);

        return back()->with('success', "✅ NFC milik {$nfc->user->name} berhasil dicabut.");
    }

    /* ========================================================================
     | BULK REVOKE — cabut banyak credential
     | ===================================================================== */
    public function bulkRevoke(Request $request)
    {
        $request->validate([
            'nfc_ids'   => 'required|array|min:1',
            'nfc_ids.*' => 'integer|exists:nfc_credentials,id',
            'reason'    => 'nullable|string|max:255',
        ]);

        $reason = (string) $request->input('reason', 'Dicabut massal oleh admin');
        $count  = 0;

        foreach ($request->input('nfc_ids') as $id) {
            $nfc = NfcCredential::find($id);
            if (! $nfc || $nfc->is_revoked) continue;

            $nfc->revoke($reason);
            $count++;
        }

        Cache::forget(self::STATS_KEY);
        $this->audit('nfc.bulk_revoke', ['count' => $count, 'reason' => $reason]);

        return back()->with('success', "✅ {$count} NFC berhasil dicabut.");
    }

    /* ========================================================================
     | DOWNLOAD — export QR sebagai PNG / JPG / PDF
     | ===================================================================== */
    public function download($user_id, $format = 'png')
    {
        $user = User::with('nfcCredential')->findOrFail($user_id);
        $nfc  = $user->nfcCredential;

        if (! $nfc || ! $nfc->is_active || $nfc->is_revoked) {
            return back()->with('error', 'NFC tidak ditemukan atau tidak aktif.');
        }

        $filename = 'NFC_' . ($user->member_number ?? $user->id) . '_' . date('YmdHis') . '.' . $format;
        $data     = $nfc->nfc_uuid;

        $getPng = function ($data) {
            $writer = new PngWriter();
            return $writer->write(new QrCode($data))->getString();
        };

        try {
            switch ($format) {
                case 'png':
                    return $this->downloadPng($getPng($data), $user, $nfc, $filename);

                case 'jpg':
                case 'jpeg':
                    $img = imagecreatefromstring($getPng($data));
                    ob_start();
                    imagejpeg($img, null, 90);
                    $content = ob_get_clean();
                    imagedestroy($img);

                    return response($content)
                        ->header('Content-Type', 'image/jpeg')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

                case 'pdf':
                    $base64 = base64_encode($getPng($data));
                    $pdf = Pdf::loadHTML(
                        '<div style="text-align:center"><h3>NFC Credential</h3><img src="data:image/png;base64,' . $base64 . '" style="width:200px"></div>'
                    )->setPaper('A4', 'portrait');

                    return $pdf->download($filename);

                default:
                    return back()->with('error', 'Format tidak didukung.');
            }
        } catch (\Throwable $e) {
            Log::error('NFC download failed', ['user' => $user_id, 'format' => $format, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal membuat NFC: ' . $e->getMessage());
        }
    }

    /* ========================================================================
     | HISTORY — riwayat NFC (termasuk soft-deleted)
     | ===================================================================== */
    public function history()
    {
        $nfcCredentials = NfcCredential::withTrashed()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('owner.management.nfc.history', compact('nfcCredentials'));
    }

    /* ========================================================================
     | DESTROY — soft delete satu credential
     | ===================================================================== */
    public function destroy($id)
    {
        $nfc = NfcCredential::findOrFail($id);
        $nfc->delete();

        Cache::forget(self::STATS_KEY);
        $this->audit('nfc.destroy', ['nfc_uuid' => $nfc->nfc_uuid, 'user_id' => $nfc->user_id]);

        return back()->with('success', 'NFC berhasil dihapus.');
    }

    /* ========================================================================
     | TESTING — halaman testing
     | ===================================================================== */
    public function testing()
    {
        return view('owner.management.nfc.testing');
    }

    /* ========================================================================
     | VALIDATE — validasi NFC (dual-mode: UUID v4 + UID fisik)
     | ===================================================================== */
  public function validateNfc(Request $request)
{
    $startTime = microtime(true);

    $request->validate(['nfc_uuid' => 'required|string|max:255']);

    $raw = trim($request->nfc_uuid);

    // ================================================================
    // DETEKSI TIPE INPUT
    // ================================================================
    $detectedUuid = null;
    $detectedUid  = null;
    $inputType    = 'unknown';

    if (preg_match('/[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}/', $raw, $m)) {
        $detectedUuid = strtolower($m[0]);
        $inputType    = 'uuid';
    } elseif (preg_match('/\b[A-F0-9]{8,64}\b/i', $raw, $m)) {
        $detectedUid  = strtoupper($m[0]);
        $inputType    = 'physical_uid';
    }

    // ================================================================
    // CARI NFC — UUID (case-insensitive) ATAU UID fisik
    // ================================================================
    $nfc = NfcCredential::query()
        ->where(function ($q) use ($detectedUuid, $detectedUid) {
            if ($detectedUuid) {
                // Pakai whereRaw LOWER() untuk case-insensitive tanpa ubah index
                $q->orWhereRaw('LOWER(nfc_uuid) = ?', [$detectedUuid]);
            }
            if ($detectedUid) {
                $q->orWhereRaw('UPPER(nfc_identifier) = ?', [$detectedUid]);
            }
        })
        ->where('is_active', true)
        ->where('is_revoked', false)
        ->with(['user.level', 'user.jabatans'])
        ->first();

    // ================================================================
    // TIDAK DITEMUKAN
    // ================================================================
    if (! $nfc) {
        if ($detectedUuid) {
            $isQrUuid = \App\Models\UserQrCode::where('qr_uuid', $detectedUuid)->exists();
            if ($isQrUuid) {
                return response()->json([
                    'valid'         => false,
                    'message'       => 'Input ini adalah QR Code biasa, bukan NFC.',
                    'input_type'    => $inputType,
                    'detected_uuid' => $detectedUuid,
                    'detected_uid'  => $detectedUid,
                    'uuid_checked'  => $raw,
                    'elapsed_ms'    => round((microtime(true) - $startTime) * 1000, 2),
                ]);
            }
        }

        return response()->json([
            'valid'         => false,
            'message'       => $inputType === 'physical_uid'
                ? 'UID fisik tidak terdaftar di sistem.'
                : 'NFC dengan UUID ini tidak ditemukan atau tidak aktif.',
            'input_type'    => $inputType,
            'detected_uuid' => $detectedUuid,
            'detected_uid'  => $detectedUid,
            'uuid_checked'  => $raw,
            'elapsed_ms'    => round((microtime(true) - $startTime) * 1000, 2),
        ]);
    }

    // ================================================================
    // MATCHED BY
    // ================================================================
    $matchedBy = null;
    if ($detectedUuid && strtolower($nfc->nfc_uuid) === $detectedUuid) {
        $matchedBy = 'uuid';
    } elseif ($detectedUid && strtoupper($nfc->nfc_identifier ?? '') === $detectedUid) {
        $matchedBy = 'physical_uid';
    }

    // ================================================================
    // AUTO-INCREMENT SCAN COUNT (audit trail)
    // ================================================================
    $nfc->increment('scan_count');
    $nfc->update(['last_used_at' => now()]);
    $nfc->refresh();

    // ================================================================
    // EXPIRY INFO
    // ================================================================
    $expiryInfo = null;
    if ($nfc->expired_at) {
        $daysLeft = now()->diffInDays($nfc->expired_at, false);
        $expiryInfo = [
            'date'       => $nfc->expired_at->format('d M Y'),
            'days_left'  => max(0, (int) $daysLeft),
            'is_expired' => $nfc->expired_at->isPast(),
        ];
    }

    // ================================================================
    // SUKSES
    // ================================================================
    return response()->json([
        'valid'      => true,
        'input_type' => $inputType,
        'matched_by' => $matchedBy,
        'elapsed_ms' => round((microtime(true) - $startTime) * 1000, 2),
        'user' => [
            'id'      => $nfc->user->id,
            'name'    => $nfc->user->name,
            'nim'     => $nfc->user->nim,
            'level'   => $nfc->user->level->name ?? 'N/A',
            'jabatan' => $nfc->user->jabatans->pluck('name')->filter()->join(', ') ?: null,
        ],
        'nfc' => [
            'uuid'          => $nfc->nfc_uuid,
            'identifier'    => $nfc->nfc_identifier,
            'status'        => $nfc->status_label,
            'status_color'  => $nfc->status_color,
            'registered_at' => $nfc->registered_at?->format('d M Y'),
            'last_used_at'  => $nfc->last_used_at?->format('d M Y H:i'),
            'scan_count'    => $nfc->scan_count ?? 0,
            'expired_at'    => $nfc->expired_at?->format('d M Y'),
            'expiry_info'   => $expiryInfo,
            'notes'         => $nfc->notes,
        ],
    ]);
}

    /* ========================================================================
     | STATS — endpoint live stats (untuk auto-refresh)
     | ===================================================================== */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'stats'   => $this->getCachedStats(),
        ]);
    }

    /* ========================================================================
     | UPLOAD IMAGE — ekstrak UUID dari gambar QR
     | ===================================================================== */
    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048']);

        try {
            $qrReader = new QrReader($request->file('image')->getRealPath());
            $text = $qrReader->text();

            if (! $text) {
                return response()->json(['error' => 'QR tidak terbaca. Pastikan gambar jelas.'], 422);
            }

            [$uuid, $uid, $type] = $this->detectInputType($text);

            return response()->json([
                'success'     => true,
                'input_type'  => $type,
                'uuid'        => $uuid ?: $text,
                'uid'         => $uid,
                'raw'         => $text,
            ]);
        } catch (\Throwable $e) {
            Log::warning('QR image read failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal membaca QR: ' . $e->getMessage()], 500);
        }
    }

    /* ========================================================================
     | UPLOAD FILE — ekstrak UUID/UID dari file .txt
     | ===================================================================== */
    public function uploadFile(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:txt,text,plain|max:1024']);

        $content = file_get_contents($request->file('file')->getRealPath());
        $content = trim($content);

        [$uuid, $uid, $type] = $this->detectInputType($content);

        return response()->json([
            'success'    => true,
            'input_type' => $type,
            'uuid'       => $uuid ?: $content,
            'uid'        => $uid,
            'raw'        => $content,
        ]);
    }

    /* ========================================================================
     | PRIVATE HELPERS
     | ===================================================================== */

    /**
     * Parse comma-separated string IDs → array of unique integers.
     */
    private function parseIds(string $raw): array
    {
        return collect(explode(',', $raw))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '' && is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Map User model → object ringan untuk view generate.
     */
    private function mapUserForGenerate(User $user): object
    {
        $latest = $user->nfcCredential;
        $active = $latest && $latest->isActive();

        return (object) [
            'id'             => $user->id,
            'name'           => $user->name,
            'nim'            => $user->nim,
            'level'          => $user->level->name ?? '-',
            'status'         => $user->status->name ?? '-',
            'jabatan'        => $user->jabatans->pluck('name')->filter()->join(', ') ?: null,
            'poin'           => (int) ($user->total_points ?? 0),
            'avatar_url'     => $user->avatar_url,
            'has_nfc'        => (bool) $latest,
            'nfc_active'     => $active,
            'nfc_uuid'       => $latest->nfc_uuid ?? null,
            'nfc_identifier' => $latest->nfc_identifier ?? null,
            'nfc_status'     => $latest ? $latest->status_label : 'Belum terdaftar',
            'can_generate'   => ! $active,
        ];
    }

    /**
     * Deteksi tipe input: UUID v4 / UID fisik / unknown.
     *
     * @return array{0: ?string, 1: ?string, 2: string} [uuid, uid, type]
     */
    private function detectInputType(string $raw): array
    {
        if (preg_match(self::UUID_PATTERN, $raw, $m)) {
            return [strtolower($m[0]), null, 'uuid'];
        }

        if (preg_match('/\b[A-F0-9]{8,64}\b/i', $raw, $m)) {
            return [null, strtoupper($m[0]), 'physical_uid'];
        }

        return [null, null, 'unknown'];
    }

    /**
     * Response saat NFC tidak ditemukan.
     */
    private function validateNotFound(?string $uuid, ?string $uid, string $type, string $raw)
    {
        // Cek apakah ini QR Code biasa
        if ($uuid && \App\Models\UserQrCode::where('qr_uuid', $uuid)->exists()) {
            return response()->json([
                'valid'         => false,
                'input_type'    => $type,
                'message'       => 'Input ini adalah QR Code biasa, bukan NFC.',
                'detected_uuid' => $uuid,
                'detected_uid'  => $uid,
                'uuid_checked'  => $raw,
            ]);
        }

        return response()->json([
            'valid'         => false,
            'input_type'    => $type,
            'message'       => $type === 'physical_uid'
                ? 'UID fisik tidak terdaftar di sistem.'
                : 'NFC dengan UUID ini tidak ditemukan atau tidak aktif.',
            'detected_uuid' => $uuid,
            'detected_uid'  => $uid,
            'uuid_checked'  => $raw,
        ]);
    }

    /**
     * Download QR sebagai PNG dengan canvas 400x500 (QR + label).
     */
    private function downloadPng(string $pngContent, User $user, NfcCredential $nfc, string $filename)
    {
        $canvas = imagecreatetruecolor(400, 500);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $black  = imagecolorallocate($canvas, 0, 0, 0);
        $gray   = imagecolorallocate($canvas, 100, 100, 100);
        imagefill($canvas, 0, 0, $white);

        $qrImg = imagecreatefromstring($pngContent);
        imagecopyresampled($canvas, $qrImg, 0, 0, 0, 0, 400, 400, imagesx($qrImg), imagesy($qrImg));
        imagedestroy($qrImg);

        $name = $user->name ?? 'NFC Credential';
        $nim  = $user->nim ?? '';
        $uid  = $nfc->nfc_identifier ?? '-';

        imagestring($canvas, 5, 20, 415, $name, $black);
        imagestring($canvas, 3, 20, 440, "NIM: {$nim}", $gray);
        imagestring($canvas, 2, 20, 462, "UID: {$uid}", $gray);

        ob_start();
        imagepng($canvas);
        $output = ob_get_clean();
        imagedestroy($canvas);

        return response($output)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get stats dengan cache.
     */
    private function getCachedStats(): array
    {
        return Cache::remember(self::STATS_KEY, self::STATS_TTL, function () {
            $totalActive = NfcCredential::active()->count();

            return [
                'total_users'   => User::count(),
                'with_nfc'      => $totalActive,
                'without_nfc'   => User::whereDoesntHave('nfcCredential', fn ($q) => $q->active())->count(),
                'revoked'       => NfcCredential::where('is_revoked', true)->count(),
                'no_uid'        => NfcCredential::active()->whereNull('nfc_identifier')->count(),
                'total_nfc'     => NfcCredential::count(),
                'active_rate'   => User::count() > 0
                    ? round(($totalActive / User::count()) * 100)
                    : 0,
            ];
        });
    }

    /**
     * Deferred QR generation — jalan setelah response terkirim.
     */
    private function deferQrGeneration(int $nfcId, string $nfcUuid): void
    {
        defer(function () use ($nfcId, $nfcUuid) {
            try {
                $writer = new PngWriter();
                $result = $writer->write(new QrCode($nfcUuid));

                $path = 'nfc_images/' . $nfcUuid . '.png';
                Storage::disk('public')->put($path, $result->getString());

                NfcCredential::withoutEvents(function () use ($nfcId, $path) {
                    NfcCredential::withTrashed()
                        ->where('id', $nfcId)
                        ->update(['nfc_image' => $path]);
                });
            } catch (\Throwable $e) {
                Log::warning('Deferred NFC QR generation failed', [
                    'nfc_id'   => $nfcId,
                    'nfc_uuid' => $nfcUuid,
                    'error'    => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * Incremental cache update — biar index tidak perlu rebuild.
     */
    private function bumpStatsCache(int $successCount, int $restoredCount = 0): void
    {
        $stats = Cache::get(self::STATS_KEY);
        if (! is_array($stats)) return;

        $stats['with_nfc']    = ($stats['with_nfc']    ?? 0) + $successCount;
        $stats['without_nfc'] = max(0, ($stats['without_nfc'] ?? 0) - $successCount);
        $stats['total_nfc']   = ($stats['total_nfc']   ?? 0) + $successCount;

        if (($stats['total_users'] ?? 0) > 0) {
            $stats['active_rate'] = round(($stats['with_nfc'] / $stats['total_users']) * 100);
        }

        Cache::put(self::STATS_KEY, $stats, self::STATS_TTL);
    }

    /**
     * Audit log helper.
     */
    private function audit(string $action, array $context = []): void
    {
        Log::channel('single')->info("[NFC] {$action}", array_merge([
            'by'   => auth()->id(),
            'ip'   => request()->ip(),
            'time' => now()->toDateTimeString(),
        ], $context));
    }
}