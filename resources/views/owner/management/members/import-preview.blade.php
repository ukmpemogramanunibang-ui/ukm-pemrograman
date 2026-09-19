@extends('layouts.roles.owner')

@section('title', 'Preview Import – Owner UNICODE')
@section('page-title', 'Preview Data Anggota')

@push('styles')
<style>
    .preview-card {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        transition: all 0.2s;
        height: auto;
        min-height: 200px;
        max-height: 520px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        word-break: break-word;
        overflow-wrap: break-word;
        position: relative;
    }
    .preview-card.valid {
        border-color: #22c55e;
        background: #f0fdf4;
    }
    .preview-card.invalid {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .preview-card.warning {
        border-color: #eab308;
        background: #fffbeb;
    }
    .dark .preview-card.valid {
        background: #1a2e1a;
        border-color: #4ade80;
    }
    .dark .preview-card.invalid {
        background: #2e1a1a;
        border-color: #f87171;
    }
    .dark .preview-card.warning {
        background: #2e2a1a;
        border-color: #fbbf24;
    }
    .preview-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .preview-card .field-group {
        margin-bottom: 0.3rem;
        border-bottom: 1px dashed #e5e7eb;
        padding-bottom: 0.2rem;
    }
    .dark .preview-card .field-group {
        border-bottom-color: #374151;
    }
    .preview-card .field-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .label {
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.04em;
    }
    .dark .label {
        color: #9ca3af;
    }
    .value {
        font-size: 0.85rem;
        color: #1f2937;
        font-weight: 500;
        word-break: break-word;
        overflow-wrap: break-word;
        line-height: 1.3;
    }
    .dark .value {
        color: #e5e7eb;
    }
    .status-badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-weight: 600;
    }
    .status-badge.valid {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }
    .status-badge.invalid {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .status-badge.warning {
        background: rgba(234, 179, 8, 0.15);
        color: #ca8a04;
    }
    .validation-errors {
        font-size: 0.65rem;
        color: #ef4444;
        background: #fef2f2;
        padding: 0.3rem 0.5rem;
        border-radius: 0.4rem;
        margin-bottom: 0.5rem;
        border-left: 3px solid #ef4444;
    }
    .dark .validation-errors {
        background: #2e1a1a;
        color: #f87171;
        border-left-color: #f87171;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-action.edit {
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.08);
    }
    .btn-action.edit:hover {
        background: rgba(245, 158, 11, 0.16);
    }
    .btn-action.delete {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.08);
    }
    .btn-action.delete:hover {
        background: rgba(239, 68, 68, 0.16);
    }
    .btn-action.save {
        color: #22c55e;
        background: rgba(34, 197, 94, 0.08);
    }
    .btn-action.save:hover {
        background: rgba(34, 197, 94, 0.16);
    }

    .avatar-preview {
        width: 64px;       /* sebelumnya 48px */
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e7eb;
    margin-bottom: 0.5rem;
    align-self: center;
    }
    .dark .avatar-preview {
        border-color: #4b5563;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 md:p-8 border border-gray-200/60 dark:border-gray-700/60">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-eye text-indigo-500"></i>
                    Preview Data Anggota
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Ditemukan {{ $data->count() }} data.
                    <span class="text-green-600 dark:text-green-400">{{ $data->filter(fn($r) => $r['_valid'] ?? false)->count() }}</span> valid,
                    <span class="text-red-600 dark:text-red-400">{{ $data->filter(fn($r) => !($r['_valid'] ?? true))->count() }}</span> tidak valid.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('owner.management.members.import.form') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <form action="{{ route('owner.management.members.import.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:shadow-md transition flex items-center gap-1.5">
                        <i class="fas fa-save"></i> Simpan Valid ({{ $data->filter(fn($r) => $r['_valid'] ?? false)->count() }})
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 auto-rows-fr">
            @foreach($data as $index => $row)
            @php
                $valid = $row['_valid'] ?? false;
                $errors = $row['_validation'] ?? [];
                $statusClass = $valid ? 'valid' : (empty($errors) ? 'warning' : 'invalid');
                $gender = $row['gender'] ?? '';
                $avatarUrl = $gender == 'Laki laki' ? asset('images/gif.gif') : ($gender == 'Perempuan' ? asset('images/gim.gif') : asset('images/logo_ukm.jpeg'));
            @endphp
            <div class="preview-card {{ $statusClass }}" data-index="{{ $index }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-400">#{{ $index + 1 }}</span>
                    <span class="status-badge {{ $statusClass }}">
                        {{ $valid ? 'Valid' : ($errors ? 'Error' : 'Peringatan') }}
                    </span>
                </div>

                {{-- Avatar --}}
                <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar-preview">

                {{-- Errors --}}
                @if(!empty($errors))
                <div class="validation-errors">
                    @foreach($errors as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
                @endif

                <div class="flex-1 space-y-0.5">
                    <div class="field-group">
                        <div class="label">Nama</div>
                        <div class="value">{{ $row['name'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">NIM</div>
                        <div class="value">{{ $row['nim'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Email</div>
                        <div class="value">{{ $row['email'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Telepon</div>
                        <div class="value">{{ $row['phone'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Alamat</div>
                        <div class="value">{{ \Illuminate\Support\Str::limit($row['address'] ?? '-', 60) }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Gender</div>
                        <div class="value">{{ $row['gender'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Laptop</div>
                        <div class="value">{{ isset($row['has_laptop']) ? ($row['has_laptop'] == 'Ya' ? '✅ Ya' : '❌ Tidak') : '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Program Studi</div>
                        <div class="value">{{ $row['major'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Angkatan</div>
                        <div class="value">{{ $row['angkatan'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Bio</div>
                        <div class="value">{{ \Illuminate\Support\Str::limit($row['bio'] ?? '-', 40) }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Universitas</div>
                        <div class="value">{{ $row['university'] ?? '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Password</div>
                        <div class="value">{{ isset($row['password']) ? '🔒 ********' : '-' }}</div>
                    </div>
                    <div class="field-group">
                        <div class="label">Semester</div>
                        <div class="value">{{ $row['semester'] ?? '-' }}</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <button onclick="editCard({{ $index }})" class="btn-action edit" data-index="{{ $index }}">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button onclick="deleteCard({{ $index }})" class="btn-action delete" data-index="{{ $index }}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        @if($data->isEmpty())
        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
            <i class="fas fa-inbox text-4xl mb-3 block"></i>
            <p>Tidak ada data yang ditemukan.</p>
        </div>
        @endif
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Edit Data Anggota</h3>
        <form id="editForm" method="POST">
            @csrf
            <input type="hidden" name="index" id="editIndex">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                    <input type="text" name="name" id="editName" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIM</label>
                    <input type="text" name="nim" id="editNim" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" id="editEmail" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telepon</label>
                    <input type="text" name="phone" id="editPhone" class="form-control">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                    <input type="text" name="address" id="editAddress" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                    <select name="gender" id="editGender" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Laki laki">Laki laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Laptop</label>
                    <select name="has_laptop" id="editHasLaptop" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Program Studi</label>
                    <input type="text" name="major" id="editMajor" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Angkatan</label>
                    <input type="text" name="angkatan" id="editAngkatan" class="form-control">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                    <textarea name="bio" id="editBio" rows="2" class="form-control"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Universitas</label>
                    <input type="text" name="university" id="editUniversity" class="form-control">
                </div>
               <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
    <div class="relative">
        <input type="password" name="password" id="editPassword" class="form-control pr-10" placeholder="Minimal 8 karakter">
        <button type="button" onclick="togglePasswordVisibility('editPassword', this)" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
    <div class="relative">
        <input type="password" name="password_confirmation" id="editPasswordConfirmation" class="form-control pr-10" placeholder="Ulangi password">
        <button type="button" onclick="togglePasswordVisibility('editPasswordConfirmation', this)" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                    <input type="text" name="semester" id="editSemester" class="form-control">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" onclick="closeEditModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
   function editCard(index) {
    fetch('{{ route("owner.management.members.import.get-card") }}?index=' + index)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            const row = data.row;
            document.getElementById('editIndex').value = index;
            document.getElementById('editName').value = row.name || '';
            document.getElementById('editNim').value = row.nim || '';
            document.getElementById('editEmail').value = row.email || '';
            document.getElementById('editPhone').value = row.phone || '';
            document.getElementById('editAddress').value = row.address || '';
            document.getElementById('editGender').value = row.gender || '';
            document.getElementById('editHasLaptop').value = row.has_laptop || '';
            document.getElementById('editMajor').value = row.major || '';
            document.getElementById('editAngkatan').value = row.angkatan || '';
            document.getElementById('editBio').value = row.bio || '';
            document.getElementById('editUniversity').value = row.university || '';
            // Jangan tampilkan password lama
            document.getElementById('editPassword').value = '';
            document.getElementById('editPasswordConfirmation').value = '';
            document.getElementById('editSemester').value = row.semester || '';
            document.getElementById('editModal').classList.remove('hidden');
        });
}

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

   document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route("owner.management.members.import.update-card") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reload halaman untuk menampilkan perubahan
            location.reload();
        } else {
            alert('Gagal update: ' + (data.error || 'Terjadi kesalahan.'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan koneksi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan';
    });
});
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
    function deleteCard(index) {
        if (!confirm('Yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.')) return;
        fetch('{{ route("owner.management.members.import.delete-card") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ index: index })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal hapus: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(() => alert('Terjadi kesalahan koneksi.'));
    }

    // Tutup modal jika klik di luar
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endpush