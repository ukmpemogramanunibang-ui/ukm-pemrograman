@extends('layouts.roles.' . auth()->user()->getRoleLayout())
@section('title', 'Surat Masuk')
@section('page-title', 'Surat Masuk')
@section('content')

<div class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold">Surat Masuk</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola, catat, teruskan, dan arsipkan surat yang diterima organisasi.</p>
        </div>
        <a href="{{ route('owner.officials.secretary.incoming-mails.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md shadow-blue-500/20">
            <i class="fas fa-plus mr-1"></i> Tambah Surat Masuk
        </a>
    </div>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Surat</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $total }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Surat Baru</p>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $baru }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Diproses</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $diproses }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Diarsipkan</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $diarsipkan }}</p>
    </div>
</div>

{{-- Pencarian & Filter --}}
<div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-700 mb-6">
    <form method="GET" action="{{ route('owner.officials.secretary.incoming-mails.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="text-sm text-gray-600 dark:text-gray-400">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor / Pengirim / Perihal" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
        </div>
        <div>
            <label class="text-sm text-gray-600 dark:text-gray-400">Status</label>
            <select name="status" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <option value="">Semua Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600 dark:text-gray-400">Kategori</label>
            <select name="kategori" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600 dark:text-gray-400">Bulan</label>
            <select name="bulan" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <option value="">Semua Bulan</option>
                @foreach($bulans as $b)
                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $b)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600 dark:text-gray-400">Tahun</label>
            <select name="tahun" class="px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <option value="">Semua Tahun</option>
                @foreach($tahuns as $t)
                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            <a href="{{ route('owner.officials.secretary.incoming-mails.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nomor Surat</th>
                    <th class="px-4 py-3 text-left">Pengirim</th>
                    <th class="px-4 py-3 text-left">Perihal</th>
                    <th class="px-4 py-3 text-left">Diterima</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mails as $index => $mail)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-3">{{ $mails->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium">{{ $mail->nomor_surat }}</td>
                        <td class="px-4 py-3">{{ $mail->pengirim }}</td>
                        <td class="px-4 py-3">{{ Str::limit($mail->perihal, 30) }}</td>
                        <td class="px-4 py-3">{{ $mail->tanggal_diterima->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $mail->status_badge }}">
                                {{ $mail->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1 flex-wrap">
                                {{-- Lihat --}}
                                <button onclick="openDetail({{ $mail->id }})" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </button>
                                {{-- Edit --}}
                                <a href="{{ route('owner.officials.secretary.incoming-mails.edit', $mail->id) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Lampiran --}}
                                @if($mail->lampiran)
                                    <a href="{{ asset('storage/' . $mail->lampiran) }}" target="_blank" class="p-1 text-green-600 hover:bg-green-50 rounded" title="Lampiran">
                                        <i class="fas fa-paperclip"></i>
                                    </a>
                                @endif
                                {{-- Teruskan --}}
                                <button onclick="openForward({{ $mail->id }})" class="p-1 text-purple-600 hover:bg-purple-50 rounded" title="Teruskan">
                                    <i class="fas fa-share"></i>
                                </button>
                                {{-- Arsipkan --}}
                                @if($mail->status != 'diarsipkan')
                                    <form action="{{ url('owner/officials/secretary/incoming-mails/' . $mail->id . '/archive') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-gray-600 hover:bg-gray-50 rounded" title="Arsipkan" onclick="return confirm('Arsipkan surat ini?')">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                @endif
                                {{-- Hapus --}}
                                <form action="{{ route('owner.officials.secretary.incoming-mails.destroy', $mail->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus surat ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                            Belum ada surat masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t dark:border-gray-700">
        {{ $mails->appends(request()->query())->links() }}
    </div>
</div>

{{-- Modal Detail --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b dark:border-gray-700 p-4 flex justify-between items-center">
                <h3 class="text-xl font-bold">Detail Surat Masuk</h3>
                <button onclick="closeDetail()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="detailContent" class="p-6">
                <div class="text-center text-gray-500">Memuat...</div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Forward --}}
<div id="forwardModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full">
            <div class="border-b dark:border-gray-700 p-4 flex justify-between items-center">
                <h3 class="text-xl font-bold">Teruskan Surat</h3>
                <button onclick="closeForward()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <form id="forwardForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" id="forwardMailId" name="mail_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Kirim kepada</label>
                    <select name="user_id" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600">
                        <option value="">Pilih...</option>
                        @foreach(\App\Models\User::whereIn('level_id', [1,2,3,4])->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->level->name ?? 'Pengurus' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Catatan</label>
                    <textarea name="catatan" rows="3" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600" placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeForward()" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Detail Modal
    function openDetail(id) {
        fetch(`{{ url('owner/officials/secretary/incoming-mails') }}/${id}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('detailContent').innerHTML = html;
                document.getElementById('detailModal').classList.remove('hidden');
            })
            .catch(() => {
                document.getElementById('detailContent').innerHTML = '<div class="text-red-500">Gagal memuat detail.</div>';
            });
    }

    function closeDetail() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // Forward Modal
    function openForward(id) {
        document.getElementById('forwardMailId').value = id;
        document.getElementById('forwardForm').action = `{{ url('owner/officials/secretary/incoming-mails') }}/${id}/forward`;
        document.getElementById('forwardModal').classList.remove('hidden');
    }

    function closeForward() {
        document.getElementById('forwardModal').classList.add('hidden');
    }

    // Tutup modal dengan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetail();
            closeForward();
        }
    });

    // Klik luar modal untuk menutup
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetail();
    });
    document.getElementById('forwardModal').addEventListener('click', function(e) {
        if (e.target === this) closeForward();
    });
</script>
@endpush
@endsection