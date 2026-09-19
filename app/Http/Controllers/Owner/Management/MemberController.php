<?php

namespace App\Http\Controllers\Owner\Management;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Major;
use App\Models\Status;
use App\Models\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MembersImport;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Collection;
use App\Models\Jabatan;        
use App\Models\JabatanUser;     
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Log;  
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function create()
    {
        $universities = University::orderBy('name')->get();
        $majors = Major::orderBy('name')->get();

        return view('owner.management.members.create', compact('universities', 'majors'));
    }
public function store(Request $request)
{
    // Validasi
    $validated = $request->validate([
        'nim' => [
            'nullable',
            'string',
            'max:20',
            'unique:users,nim',
            'regex:/^[0-9]+$/',
        ],
       'name' => [
    'required',
    'string',
    'max:255',
    'min:3',
    'regex:/^[a-zA-Z\s\.\'’]+$/',
    'unique:users,name',
],
        'phone' => [
            'nullable',
            'string',
            'max:20',
            'regex:/^[0-9\-]+$/',
        ],
        'email' => [
            'required',
            'email:rfc,dns',
            'max:255',
            'unique:users,email',
        ],
        'gender' => ['nullable', Rule::in(['male', 'female'])],
        'bio' => ['nullable', 'string', 'max:1000'],
        'has_laptop' => 'nullable|boolean',
        'university_id' => 'nullable|exists:universities,id',
        'major_id' => 'nullable|exists:majors,id',
        'password' => 'required|string|min:8|confirmed',
         'major'      => 'nullable|string|max:255',
        'university' => 'nullable|string|max:255',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048', // 2MB
        ], [
        // Pesan error kustom
        'name.unique' => 'Nama lengkap sudah terdaftar. Silakan gunakan nama lain.',
        'nim.unique'  => 'NIM sudah terdaftar. Silakan gunakan NIM yang berbeda.',
        'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
    ]);

    // === SANITASI BIO ===
    if (!empty($validated['bio'])) {
        $cleanedBio = strip_tags($validated['bio']);
        $forbiddenPatterns = [
            '/\beval\s*\(/i', '/\bexec\s*\(/i', '/\bsystem\s*\(/i',
            '/\bpassthru\s*\(/i', '/\bshell_exec\s*\(/i', '/\bpopen\s*\(/i',
            '/\bproc_open\s*\(/i', '/\bpcntl_exec\s*\(/i', '/\bassert\s*\(/i',
            '/\binclude\s*\(/i', '/\brequire\s*\(/i', '/\binclude_once\s*\(/i',
            '/\brequire_once\s*\(/i', '/\bfopen\s*\(/i', '/\bfwrite\s*\(/i',
            '/\bfread\s*\(/i', '/\bunlink\s*\(/i', '/\brmdir\s*\(/i',
            '/\bmkdir\s*\(/i', '/\bchmod\s*\(/i', '/\bchown\s*\(/i',
            '/\bsymlink\s*\(/i', '/\blink\s*\(/i', '/\bcurl_exec\s*\(/i',
            '/\bhttp_get\s*\(/i', '/\bfile_get_contents\s*\(/i',
            '/\bfile_put_contents\s*\(/i', '/\bstream_socket_client\s*\(/i',
            '/\bfsockopen\s*\(/i', '/\bpfsockopen\s*\(/i',
            '/`.*`/', '/<\?php/i', '/<script/i', '/\bon\w+\s*=/i',
        ];
        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $cleanedBio)) {
                return back()
                    ->withInput()
                    ->withErrors(['bio' => 'Bio mengandung kode atau perintah yang tidak diizinkan.']);
            }
        }
        $validated['bio'] = $cleanedBio;
    }
 // Proses university: cari atau buat baru
$universityId = null;
if (!empty($validated['university'])) {
    $university = University::firstOrCreate(
        ['name' => $validated['university']],
        ['is_active' => true]
    );
    $universityId = $university->id;
} else {
    $defaultUniversity = University::firstOrCreate(
        ['name' => 'Universitas Tidak Diketahui'],
        ['is_active' => true]
    );
    $universityId = $defaultUniversity->id;
}

// Proses major: cari atau buat baru
$majorId = null;
if (!empty($validated['major'])) {
    $major = Major::firstOrCreate(
        [
            'name' => $validated['major'],
            'university_id' => $universityId,
        ],
        ['is_active' => true]
    );
    $majorId = $major->id;
}
    // Default level & status
    $level = Level::where('name', 'Anggota')->first() ?? Level::find(5);
    $status = Status::where('name', 'aktif')->first() ?? Status::find(2);

    // Buat user
    $user = new User();
    $user->user_uuid = (string) Str::uuid();
    $user->nim = $validated['nim'] ?? null;
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->phone = $validated['phone'] ?? null;
    $user->gender = $validated['gender'] ?? null;
    $user->bio = $validated['bio'] ?? null;
    $user->university_id = $validated['university_id'] ?? null;
    $user->major_id = $validated['major_id'] ?? null;
    $user->password = Hash::make($validated['password']);
    $user->level_id = $level->id ?? 5;
    $user->status_id = $status->id ?? 2;
    $user->account_type = 'official';
    $user->member_number = $this->generateMemberNumber();
    $user->ukm_id = 1;
    $user->is_active = true;
    $user->is_verified = false;
    $user->timezone = 'Asia/Jakarta';
    $user->locale = 'id';
    $user->receive_email_notification = true;
    $user->receive_whatsapp_notification = true;
  $user->major_id = $majorId;
    $user->university_id = $universityId;
    // Simpan preferensi laptop
    $preferences = [];
    if (isset($validated['has_laptop'])) {
        $preferences['has_laptop'] = (bool) $validated['has_laptop'];
    }
    $user->preferences = $preferences;

    $user->save();

   // === UPLOAD AVATAR ATAU DEFAULT ===
    if ($request->hasFile('avatar')) {
        // Upload file ke storage/app/public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar_url = $path;
        $user->save();
    } else {
        // Set default avatar berdasarkan gender
        $name = urlencode($user->name);
        if ($user->gender == 'male') {
            $defaultUrl =  asset('images/gif.gif');
        } elseif ($user->gender == 'female') {
            $defaultUrl = asset('images/gim.gif');
        } else {
            // Jika gender tidak diisi, gunakan warna abu-abu
            $defaultUrl = asset('images/logo_ukm.jpeg');
        }
        $user->avatar_url = $defaultUrl;
        $user->save();
    }

    return redirect()
        ->route('owner.management.members.index')
        ->with('success', 'Anggota berhasil ditambahkan.');
}

public function getPromoteData()
{
    try {
        $users = User::with(['level', 'jabatans'])->get();
        $data = $users->map(function ($user) {
            return [
                'id'            => $user->id,
                'nim'           => $user->nim ?? '-',
                'name'          => $user->name,
                'email'         => $user->email,
                'level_name'    => $user->level ? $user->level->name : null, // perbaiki
                'jabatan_names' => $user->jabatans->pluck('name')->implode(', '),
                'total_points'  => $user->total_points ?? 0, // pastikan field ini ada atau gunakan 0
                'is_active'     => $user->is_active,
            ];
        });
        return response()->json($data);
    } catch (\Exception $e) {
        \Log::error('getPromoteData error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function getJabatans()
    {
        $jabatans = Jabatan::all(['id', 'name']);
        return response()->json($jabatans);
    }

    /**
     * Proses update pangkat / jabatan
     */
    public function updatePromoteDemote(Request $request)
{
    // Validasi
    $request->validate([
        'user_ids' => 'required|array|min:1',
        'user_ids.*' => 'exists:users,id',
        'action' => 'required|in:promote,demote,add_position,remove_position',
        'target_role' => 'nullable|string',
        'jabatan_ids' => 'nullable|array',
        'jabatan_ids.*' => 'exists:jabatans,id',
        'update_password' => 'nullable|boolean',
        'superadmin_pin' => 'nullable|string',
    ]);

    $userIds = $request->user_ids;
    $action = $request->action;
    $targetRole = $request->target_role;
    $jabatanIds = $request->jabatan_ids ?? [];
    $updatePassword = $request->boolean('update_password');
    $superadminPin = $request->input('superadmin_pin');

    // Cek jika target superadmin, PIN wajib
    if ($targetRole === 'superadmin') {
        if (empty($superadminPin) || $superadminPin !== config('app.superadmin_pin', '1234567890')) {
            return response()->json([
                'success' => false,
                'message' => 'PIN Superadmin salah!'
            ], 422);
        }
    }

    // Cek role terlarang (Owner tidak boleh diubah)
    $users = User::whereIn('id', $userIds)->get();
    foreach ($users as $user) {
        if ($user->level_id == 1) {
            return response()->json([
                'success' => false,
                'message' => "User {$user->name} adalah Owner, tidak dapat diubah!"
            ], 422);
        }
    }

    DB::beginTransaction();
    try {
        // Mapping level
        $levelMap = [
            'superadmin' => 2,
            'ketua' => 3,
            'pengurus' => 4,
            'anggota' => 5,
            'anggota_umum' => 6,
            'pengunjung' => 7,
        ];

        foreach ($users as $user) {
            $currentLevel = $user->level_id;

            // 1. Handle promote / demote
            if ($action === 'promote' || $action === 'demote') {
                $newLevel = $levelMap[$targetRole] ?? $currentLevel;
                if ($newLevel !== $currentLevel) {
                    // Cek jika target superadmin, pastikan ada PIN
                    if ($targetRole === 'superadmin') {
                        // PIN sudah divalidasi di atas
                    }
                    $user->level_id = $newLevel;
                }
            }

            // 2. Handle add/remove position (jabatan)
            if ($action === 'add_position') {
                $user->jabatans()->syncWithoutDetaching($jabatanIds);
            } elseif ($action === 'remove_position') {
                $user->jabatans()->detach($jabatanIds);
            }

            // 3. Update password & pin jika checkbox dicentang
            if ($updatePassword) {
                $newPassword = $this->generateRandomPassword();
                $newPin = $this->generateRandomPin();

                $user->password = Hash::make($newPassword);
                $user->pin = Hash::make($newPin);
                $user->save();

                // Simpan notifikasi atau log (opsional)
                Log::info("Password & PIN diperbarui untuk user {$user->name} oleh Owner", [
                    'user_id' => $user->id,
                    'new_password' => $newPassword,
                    'new_pin' => $newPin,
                    'updated_by' => auth()->id(),
                ]);

                // Simpan ke session untuk ditampilkan ke Owner (opsional)
                $updatedCredentials[$user->id] = [
                    'password' => $newPassword,
                    'pin' => $newPin,
                ];
            }

            $user->save();
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Perubahan berhasil disimpan.',
            'credentials' => $updatedCredentials ?? null,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updatePromoteDemote: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

// Helper generate random password
private function generateRandomPassword($length = 12)
{
    return Str::random($length);
}

// Helper generate random pin (6 digit)
private function generateRandomPin()
{
    return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}
    // Fungsi bantu untuk level (sesuaikan dengan level_id di database Anda)
    private function getHigherLevelId($currentLevelId)
    {
        // Asumsikan level_id: 1=owner, 2=superadmin, 3=ketua, 4=pengurus, 5=anggota, 6=guest
        // Urutan naik: guest(6) -> anggota(5) -> pengurus(4) -> ketua(3) -> superadmin(2) -> owner(1)
        $levels = [6, 5, 4, 3, 2, 1]; // dari rendah ke tinggi
        $index = array_search($currentLevelId, $levels);
        if ($index === false || $index >= count($levels) - 1) {
            return $currentLevelId; // sudah tertinggi
        }
        return $levels[$index + 1];
    }

    private function getLowerLevelId($currentLevelId)
    {
        $levels = [6, 5, 4, 3, 2, 1];
        $index = array_search($currentLevelId, $levels);
        if ($index === false || $index <= 0) {
            return $currentLevelId; // sudah terendah
        }
        return $levels[$index - 1];
    }

    
private function processUser($user, $action, $targetRole, $jabatanIds)
{
    // Dapatkan level_id berdasarkan target_role
    $level = Level::where('name', $targetRole)->first();
    if (!$level) throw new \Exception("Level $targetRole tidak ditemukan.");

    // Ambil jabatan-jabatan yang sudah dimiliki user (aktif)
    $currentJabatanIds = $user->jabatans()->wherePivot('status', 'active')->pluck('jabatans.id')->toArray();

    if ($action === 'promote') {
        // Naikkan level
        $user->level_id = $level->id;
        $user->save();
        // Tambahkan jabatan default sesuai level (misal: untuk pengurus, tambahkan jabatan 'Anggota' sebagai base?)
        // Kita akan tambahkan jabatan "Anggota" jika belum punya
        $this->ensureBaseJabatan($user, $level);

    } elseif ($action === 'demote') {
        // Turunkan level
        $user->level_id = $level->id;
        $user->save();
        // Pastikan user memiliki jabatan dasar
        $this->ensureBaseJabatan($user, $level);

    } elseif ($action === 'add_position') {
        // Tambahkan jabatan baru
        foreach ($jabatanIds as $jid) {
            if (!in_array($jid, $currentJabatanIds)) {
                $user->jabatans()->attach($jid, [
                    'is_primary' => false,
                    'status' => 'active',
                    'created_at' => now()
                ]);
            }
        }
        // Set level tertinggi berdasarkan jabatan (update level sesuai jabatan tertinggi)
        $this->updateLevelFromJabatans($user);

    } elseif ($action === 'remove_position') {
        // Kurangi jabatan (nonaktifkan)
        foreach ($jabatanIds as $jid) {
            $user->jabatans()->updateExistingPivot($jid, ['status' => 'inactive', 'end_date' => now()]);
        }
        // Update level
        $this->updateLevelFromJabatans($user);
    }

    // Pastikan jumlah jabatan aktif minimal 1 dan maksimal 3
    $activeJabatanIds = $user->jabatans()->wherePivot('status', 'active')->pluck('jabatans.id')->toArray();
    if (count($activeJabatanIds) < 1) {
        // Tambahkan jabatan default 'Anggota' jika tidak ada
        $anggotaJabatan = Jabatan::where('name', 'Anggota UKM')->first() ?? Jabatan::first();
        $user->jabatans()->attach($anggotaJabatan->id, ['status' => 'active', 'created_at' => now()]);
    }
    if (count($activeJabatanIds) > 3) {
        throw new \Exception("User {$user->name} memiliki lebih dari 3 jabatan aktif. Maksimal 3.");
    }
}

private function ensureBaseJabatan($user, $level)
{
    // Jika level adalah pengurus, pastikan ada jabatan yang sesuai
    $baseJabatanName = $level->name === 'pengurus' ? 'Pengurus' : ($level->name === 'ketua' ? 'Ketua' : ($level->name === 'superadmin' ? 'Superadmin' : 'Anggota UKM'));
    $jabatan = Jabatan::where('name', $baseJabatanName)->first();
    if (!$jabatan) return;

    $current = $user->jabatans()->wherePivot('status', 'active')->pluck('jabatans.id')->toArray();
    if (!in_array($jabatan->id, $current)) {
        $user->jabatans()->attach($jabatan->id, ['status' => 'active', 'created_at' => now()]);
    }
}

private function updateLevelFromJabatans($user)
{
    $activeJabatans = $user->jabatans()->wherePivot('status', 'active')->pluck('name')->toArray();

    // Tentukan level tertinggi berdasarkan nama jabatan
    $highestLevel = 'guest';
    if (in_array('Superadmin', $activeJabatans)) {
        $highestLevel = 'superadmin';
    } elseif (in_array('Ketua', $activeJabatans) || in_array('Wakil', $activeJabatans)) {
        $highestLevel = 'ketua';
    } elseif (array_intersect($activeJabatans, ['Bendahara', 'Sekretaris', 'PJ Divisi', 'PJ Kelompok', 'Assisten'])) {
        $highestLevel = 'pengurus';
    } elseif (in_array('Anggota UKM', $activeJabatans)) {
        $highestLevel = 'anggota';
    }

    $level = Level::where('name', $highestLevel)->first();
    if ($level) {
        $user->level_id = $level->id;
        $user->save();
    }
}
public function syncMembers()
{
    // Ambil semua user dengan level_id = 5 (anggota), kecuali "Anggota Umum"
    $members = User::where('level_id', 5)
                   ->where('name', '!=', 'Anggota Umum')
                   ->get();

    $updated = 0;
    $errors = [];

    foreach ($members as $member) {
        try {
            $changed = false;

            // 1. Pastikan status_id = 2 (aktif)
            if ($member->status_id != 2) {
                $member->status_id = 2;
                $changed = true;
            }

            // 2. Pastikan is_active = true
            if (!$member->is_active) {
                $member->is_active = true;
                $changed = true;
            }

            // 3. Pastikan account_type = 'official'
            if ($member->account_type != 'official') {
                $member->account_type = 'official';
                $changed = true;
            }

            // 4. Pastikan member_number tidak kosong dan unik
            if (empty($member->member_number) || User::where('member_number', $member->member_number)->where('id', '!=', $member->id)->exists()) {
                $member->member_number = $this->generateMemberNumber();
                $changed = true;
            }

            // 5. Pastikan is_verified = true (jika email terverifikasi)
            if ($member->email && !$member->is_verified) {
                $member->is_verified = true;
                $member->verified_at = now();
                $changed = true;
            }

            // 6. Jika ada soft delete, pulihkan (jika perlu)
            if ($member->deleted_at !== null) {
                $member->deleted_at = null;
                $changed = true;
            }

            if ($changed) {
                $member->save();
                $updated++;
            }
        } catch (\Exception $e) {
            $errors[] = "Gagal sinkronisasi {$member->email}: " . $e->getMessage();
        }
    }

    $message = "Sinkronisasi selesai! {$updated} anggota diperbarui.";
    if (!empty($errors)) {
        $message .= " Error: " . implode('; ', $errors);
    }

    return redirect()->route('owner.management.members.index')
                     ->with('success', $message);
}
public function checkUnique(Request $request)
{
    $field = $request->input('field'); // 'nim' atau 'name'
    $value = $request->input('value');

    if (!in_array($field, ['nim', 'name'])) {
        return response()->json(['valid' => false, 'message' => 'Field tidak valid']);
    }

    $exists = User::where($field, $value)->exists();
    return response()->json([
        'valid' => !$exists,
        'message' => $exists ? ucfirst($field) . ' sudah terdaftar. Silakan gunakan yang lain.' : null,
    ]);
}
    private function generateMemberNumber(): string
{
    $prefix = 'UKM-ANG-';
    $maxAttempts = 10;
    $attempt = 0;

    do {
        $last = User::where('member_number', 'LIKE', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();
        if ($last) {
            $lastNumber = (int) substr($last->member_number, strlen($prefix));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        $candidate = $prefix . $newNumber;
        $attempt++;
    } while (User::where('member_number', $candidate)->exists() && $attempt < $maxAttempts);

    return $candidate;
}
    public function importForm()
{
    return view('owner.management.members.import');
}

public function importPreview(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,doc,docx,pdf|max:10240',
    ]);

    $file = $request->file('file');
    $extension = $file->getClientOriginalExtension();
    $data = collect();

    if (in_array($extension, ['xlsx', 'xls'])) {
        $rows = Excel::toCollection(null, $file);
        $data = $this->parseExcelData($rows);
    } elseif (in_array($extension, ['doc', 'docx'])) {
        $data = $this->parseWordData($file);
    } elseif ($extension === 'pdf') {
        $data = $this->parsePdfData($file);
    }

    if ($data->isEmpty()) {
        return back()->withErrors(['file' => 'Tidak ada data yang ditemukan atau format tidak sesuai.']);
    }

    // Simpan data ke session
    session(['import_data' => $data]);

    return view('owner.management.members.import-preview', compact('data'));
}

private function parseExcelData($rows)
{
    $bestData = collect();
    $bestScore = 0;

    // Mapping kolom yang diharapkan
    $map = [
        'email' => 'email',
        'nama_lengkap' => 'name',
        'nama' => 'name',
        'nim' => 'nim',
        'nomor_telepon' => 'phone',
        'nomor' => 'phone',
        'alamat' => 'address',
        'gender' => 'gender',
        'memiliki_laptop' => 'has_laptop',
        'laptop' => 'has_laptop',
        'program_studi' => 'major',
        'prodi' => 'major', // tambahan untuk sheet pertama
        'angkatan' => 'angkatan',
        'bio' => 'bio',
        'universitas' => 'university',
        'password' => 'password',
        'konfirmasi_password' => 'password_confirmation',
        'semester' => 'semester',
    ];

    // Kolom prioritas yang harus ada di sheet yang dipilih
    $priorityColumns = ['program_studi', 'universitas', 'password', 'semester'];

    foreach ($rows as $sheetIndex => $sheet) {
        $headers = [];
        $normalizedHeaders = [];
        $found = false;

        // Cari baris header
        foreach ($sheet as $rowIndex => $row) {
            $rowArray = $row->toArray();
            $rowString = implode(' ', array_map('strtolower', $rowArray));
            if (str_contains($rowString, 'nama') || str_contains($rowString, 'email') || str_contains($rowString, 'nim')) {
                $headers = $rowArray;
                $found = true;
                break;
            }
        }
        if (!$found) continue;

        // Normalisasi header
        foreach ($headers as $colIndex => $colName) {
            $colName = trim($colName);
            $colName = preg_replace('/\s+/', ' ', $colName);
            $colName = strtolower($colName);
            $colName = str_replace(' ', '_', $colName);
            $colName = str_replace(['(', ')', '/', '\\', '-'], '', $colName);
            $normalizedHeaders[$colIndex] = $colName;
        }

        // Hitung skor berdasarkan kolom prioritas yang ada
        $score = 0;
        foreach ($priorityColumns as $col) {
            if (in_array($col, $normalizedHeaders)) {
                $score++;
            }
        }

        // Jika skor lebih baik, parse sheet ini
        if ($score > $bestScore) {
            $bestScore = $score;
            $sheetData = $this->parseSheetData($sheet, $normalizedHeaders, $map);
            $bestData = $sheetData;
        }

        // Jika sudah mendapatkan skor maksimal (semua prioritas ada), hentikan pencarian
        if ($bestScore == count($priorityColumns)) {
            break;
        }
    }

    return $bestData;
}

/**
 * Helper untuk mem-parsing satu sheet dengan header yang sudah dinormalisasi
 */
private function parseSheetData($sheet, $normalizedHeaders, $map)
{
    $data = collect();
    $isHeader = true;

    foreach ($sheet as $row) {
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        $rowArray = $row->toArray();
        if (empty(array_filter($rowArray))) continue;

        $rowData = [];
        foreach ($normalizedHeaders as $colIndex => $normalizedCol) {
            $value = $rowArray[$colIndex] ?? null;
            if (is_numeric($value) && !is_int($value) && strpos((string)$value, '.') !== false) {
                $value = (string) $value;
            } elseif (is_float($value)) {
                $value = (string) $value;
            }

            $field = null;
            foreach ($map as $key => $fieldName) {
                if (strpos($normalizedCol, $key) !== false) {
                    $field = $fieldName;
                    break;
                }
            }
            if ($field) {
                if ($value !== null) {
                    $value = trim($value);
                    if ($value === '') $value = null;
                }
                $rowData[$field] = $value;
            }
        }

        if (!empty($rowData['name']) || !empty($rowData['email'])) {
            // Validasi
            $validated = $this->validateRow($rowData);
            $rowData['_validation'] = $validated['_validation'];
            $rowData['_valid'] = $validated['_valid'];
            $data->push($rowData);
        }
    }

    return $data;
}
private function parseWordData($file)
{
    $phpWord = IOFactory::load($file);
    $text = '';
    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText() . "\n";
            }
        }
    }
    return $this->parseTextToCollection($text);
}

private function parsePdfData($file)
{
    $parser = new Parser();
    $pdf = $parser->parseFile($file);
    $text = $pdf->getText();
    return $this->parseTextToCollection($text);
}

private function parseTextToCollection($text)
{
    $lines = explode("\n", $text);
    $headers = [];
    $data = collect();
    $foundHeader = false;

    foreach ($lines as $index => $line) {
        $line = trim($line);
        if (empty($line)) continue;

        // Coba deteksi header: jika ada kata "nama" atau "email" atau "nim"
        if (!$foundHeader && (stripos($line, 'nama') !== false || stripos($line, 'email') !== false || stripos($line, 'nim') !== false)) {
            $headers = array_map('trim', explode("\t", $line));
            $foundHeader = true;
            continue;
        }

        if ($foundHeader) {
            $row = array_map('trim', explode("\t", $line));
            if (count($row) < 2) continue;

            // Normalisasi header
            $normalizedHeaders = [];
            foreach ($headers as $colIndex => $colName) {
                $colName = trim($colName);
                $colName = preg_replace('/\s+/', ' ', $colName);
                $colName = strtolower($colName);
                $colName = str_replace(' ', '_', $colName);
                $normalizedHeaders[$colIndex] = $colName;
            }

            $map = [
                'email' => 'email',
                'nama_lengkap' => 'name',
                'nama' => 'name',
                'nim' => 'nim',
                'nomor_telepon' => 'phone',
                'nomor' => 'phone',
                'alamat' => 'address',
                'gender' => 'gender',
                'memiliki_laptop' => 'has_laptop',
                'laptop' => 'has_laptop',
                'program_studi' => 'major',
                'angkatan' => 'angkatan',
                'bio' => 'bio',
                'universitas' => 'university',
                'password' => 'password',
                'konfirmasi_password' => 'password_confirmation',
                'semester' => 'semester',
            ];

            $rowData = [];
            foreach ($normalizedHeaders as $colIndex => $normalizedCol) {
                $value = $row[$colIndex] ?? null;
                if ($value !== null) {
                    $value = trim($value);
                    if ($value === '') $value = null;
                }
                $field = null;
                foreach ($map as $key => $fieldName) {
                    if (strpos($normalizedCol, $key) !== false) {
                        $field = $fieldName;
                        break;
                    }
                }
                if ($field) {
                    $rowData[$field] = $value;
                }
            }

            if (!empty($rowData['name']) || !empty($rowData['email'])) {
                $data->push($rowData);
            }
        }
    }

    return $data;
}
public function importGetCard(Request $request)
{
    $index = $request->input('index');
    $data = session('import_data');
    if (!$data || !$data->has($index)) {
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }
    return response()->json(['row' => $data->get($index)]);
}

public function importUpdateCard(Request $request)
{
    $index = $request->input('index');
    $data = session('import_data');
    if (!$data || !$data->has($index)) {
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    $row = $data->get($index);
    $updates = $request->except(['index', '_token']);

    // Gabungkan update dengan data lama (jika field kosong, tetap pakai nilai lama)
    foreach ($row as $key => $value) {
        if (array_key_exists($key, $updates) && $updates[$key] !== null) {
            $row[$key] = $updates[$key];
        }
    }

    // Jika password diisi, update; jika kosong, pertahankan password lama
    if (empty($updates['password'])) {
        // Password tidak diisi, pertahankan yang lama
        // Tapi kita tidak punya password lama di session (hanya hash di DB)
        // Kita bisa skip atau minta input ulang
    } else {
        // Password baru diisi
        if (strlen($updates['password']) < 8) {
            return response()->json(['error' => 'Password minimal 8 karakter.'], 422);
        }
        // Simpan password baru (akan di-hash saat store)
        $row['password'] = $updates['password'];
    }

    // Re-validasi
    $validated = $this->validateRow($row);
    $row['_validation'] = $validated['_validation'];
    $row['_valid'] = $validated['_valid'];

    $data->put($index, $row);
    session(['import_data' => $data]);

    return response()->json(['success' => true, 'row' => $row]);
}

public function importDeleteCard(Request $request)
{
    $index = $request->input('index');
    $data = session('import_data');
    if (!$data || !$data->has($index)) {
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }
    $data->forget($index);
    $data = $data->values(); // reindex
    session(['import_data' => $data]);
    return response()->json(['success' => true, 'count' => $data->count()]);
}

private function validateRow($row, $isUpdate = false)
{
    $validation = [];
    $valid = true;

    // Nama: huruf, spasi, titik, APOSTROF (' dan ’)
    if (!empty($row['name'])) {
        if (!preg_match('/^[a-zA-Z\s\.\'’]+$/', $row['name'])) {
            $validation[] = 'Nama hanya boleh huruf, spasi, titik, dan apostrof.';
            $valid = false;
        } elseif (strlen($row['name']) < 3) {
            $validation[] = 'Nama minimal 3 huruf.';
            $valid = false;
        }
    } else {
        $validation[] = 'Nama wajib diisi.';
        $valid = false;
    }

    // NIM: hanya angka
    if (!empty($row['nim'])) {
        if (!preg_match('/^[0-9]+$/', $row['nim'])) {
            $validation[] = 'NIM hanya boleh angka.';
            $valid = false;
        } elseif (strlen($row['nim']) > 20) {
            $validation[] = 'NIM maksimal 20 digit.';
            $valid = false;
        }
    }

    // Email: valid format
    if (!empty($row['email'])) {
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $validation[] = 'Email tidak valid (harus mengandung @ dan domain).';
            $valid = false;
        }
    } else {
        $validation[] = 'Email wajib diisi.';
        $valid = false;
    }

    // Telepon: hanya angka dan -
    if (!empty($row['phone'])) {
        if (!preg_match('/^[0-9\-]+$/', $row['phone'])) {
            $validation[] = 'Telepon hanya boleh angka dan tanda hubung (-).';
            $valid = false;
        }
    }

    // Universitas: hanya huruf, spasi, titik
    if (!empty($row['university'])) {
        if (!preg_match('/^[a-zA-Z\s\.]+$/', $row['university'])) {
            $validation[] = 'Universitas hanya boleh huruf, spasi, dan titik.';
            $valid = false;
        }
    }

    if ($isUpdate) {
        // Jika password diisi, validasi; jika tidak, abaikan
        if (!empty($row['password'])) {
            if (strlen($row['password']) < 8) {
                $validation[] = 'Password minimal 8 karakter.';
                $valid = false;
            }
            if (!empty($row['password_confirmation']) && $row['password'] !== $row['password_confirmation']) {
                $validation[] = 'Password dan konfirmasi password tidak cocok.';
                $valid = false;
            }
        }
        // Jika password_confirmation diisi tapi password kosong, beri pesan
        if (empty($row['password']) && !empty($row['password_confirmation'])) {
            $validation[] = 'Password tidak boleh kosong jika konfirmasi diisi.';
            $valid = false;
        }
    } else {
        // Untuk create/import baru: password wajib
        if (empty($row['password'])) {
            $validation[] = 'Password wajib diisi.';
            $valid = false;
        } else {
            if (strlen($row['password']) < 8) {
                $validation[] = 'Password minimal 8 karakter.';
                $valid = false;
            }
            if (!empty($row['password_confirmation']) && $row['password'] !== $row['password_confirmation']) {
                $validation[] = 'Password dan konfirmasi password tidak cocok.';
                $valid = false;
            }
        }
    }

    // Cek duplikat di database
    if (!empty($row['email']) && User::where('email', $row['email'])->exists()) {
        $validation[] = "Email {$row['email']} sudah terdaftar.";
        $valid = false;
    }
    if (!empty($row['nim']) && User::where('nim', $row['nim'])->exists()) {
        $validation[] = "NIM {$row['nim']} sudah terdaftar.";
        $valid = false;
    }

    return [
        '_validation' => $validation,
        '_valid' => $valid,
    ];
}
public function importStore(Request $request)
{
    $data = session('import_data');
// Hanya ambil data yang valid
$data = $data->filter(fn($row) => $row['_valid'] ?? false);
if ($data->isEmpty()) {
    return redirect()->route('owner.management.members.import.form')
                     ->withErrors(['error' => 'Tidak ada data valid untuk disimpan.']);
}

    $created = 0;
    $errors = [];

    foreach ($data as $row) {
        $name = $row['name'] ?? 'tanpa nama';

        // 1. Validasi dasar
        if (empty($row['name']) || empty($row['email']) || empty($row['password'])) {
            $errors[] = "Data untuk {$name} tidak lengkap (nama, email, password wajib).";
            continue;
        }

        // 2. Cek duplikat email & NIM
        if (User::where('email', $row['email'])->exists()) {
            $errors[] = "Email {$row['email']} sudah terdaftar.";
            continue;
        }
        if (!empty($row['nim']) && User::where('nim', $row['nim'])->exists()) {
            $errors[] = "NIM {$row['nim']} sudah terdaftar.";
            continue;
        }

        // 3. Validasi password minimal 8 karakter (jika diisi)
        if (strlen($row['password']) < 8) {
            $errors[] = "Password untuk {$name} minimal 8 karakter.";
            continue;
        }

        try {
            // 4. Simpan user
            $this->createUserFromRow($row);
            $created++;
        } catch (\Exception $e) {
            $errors[] = "Gagal simpan {$row['email']}: " . $e->getMessage();
        }
    }

    // Hapus session setelah proses
    session()->forget('import_data');

    // Redirect ke halaman sukses dengan jumlah data berhasil disimpan
    return redirect()->route('owner.management.members.import.success', ['count' => $created])
                     ->with('success', "Berhasil membuat {$created} anggota.")
                     ->with('errors', $errors); // Kirim error untuk ditampilkan (opsional)
}
public function importSuccess(Request $request)
{
    $count = $request->input('count', 0);
    return view('owner.management.members.import-success', compact('count'));
}
private function createUserFromRow($row)
{
    $level = Level::where('name', 'Anggota')->first() ?? Level::find(5);
    $status = Status::where('name', 'aktif')->first() ?? Status::find(2);

    // === PROSES UNIVERSITY ===
    $universityId = null;
    if (!empty($row['university'])) {
        $university = University::firstOrCreate(
            ['name' => $row['university']],
            ['is_active' => true]
        );
        $universityId = $university->id;
    } else {
        $defaultUniversity = University::firstOrCreate(
            ['name' => 'Universitas Tidak Diketahui'],
            ['is_active' => true]
        );
        $universityId = $defaultUniversity->id;
    }

    // === PROSES MAJOR ===
    $majorId = null;
    if (!empty($row['major'])) {
        $major = Major::firstOrCreate(
            [
                'name' => $row['major'],
                'university_id' => $universityId,
            ],
            ['is_active' => true]
        );
        $majorId = $major->id;
    }

    // === KONVERSI GENDER ===
    $gender = null;
    if (!empty($row['gender'])) {
        $genderLower = strtolower($row['gender']);
        if (strpos($genderLower, 'laki') !== false || strpos($genderLower, 'male') !== false) {
            $gender = 'male';
        } elseif (strpos($genderLower, 'perempuan') !== false || strpos($genderLower, 'female') !== false) {
            $gender = 'female';
        }
    }

    // === BUAT USER ===
    $user = new User();
    $user->user_uuid = (string) Str::uuid();
    $user->nim = $row['nim'] ?? null;
    $user->name = $row['name'];
    $user->email = $row['email'];
    $user->phone = $row['phone'] ?? null;
    $user->gender = $gender;
    $user->bio = $row['bio'] ?? null;
    $user->address = $row['address'] ?? null;
    $user->major_id = $majorId;
    $user->university_id = $universityId;
    $user->password = Hash::make($row['password']);
    $user->level_id = $level->id ?? 5;
    $user->status_id = $status->id ?? 2;
    $user->account_type = 'official';
    $user->member_number = $this->generateMemberNumber();
    $user->ukm_id = 1;
    $user->is_active = true;
    $user->is_verified = false;
    $user->timezone = 'Asia/Jakarta';
    $user->locale = 'id';
    $user->receive_email_notification = true;
    $user->receive_whatsapp_notification = true;

    $preferences = [];
    if (isset($row['has_laptop'])) {
        $preferences['has_laptop'] = filter_var($row['has_laptop'], FILTER_VALIDATE_BOOLEAN);
    }
    $user->preferences = $preferences;

    // Default avatar
    $name = urlencode($user->name);
    if ($gender == 'male') {
        $defaultUrl = asset('images/gif.gif');
    } elseif ($gender == 'female') {
        $defaultUrl = asset('images/gim.gif');
    } else {
        $defaultUrl = asset('images/logo_ukm.jpeg');
    }
    $user->avatar_url = $defaultUrl;

    $user->save();
}
}