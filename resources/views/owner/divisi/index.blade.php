@extends('layouts.roles.owner')

@section('title', 'Semua Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-sitemap text-rose-500"></i>
                Semua Divisi
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Kelola seluruh divisi yang tersedia di UKM UNICODE.
            </p>
        </div>
        <a href="{{ route('owner.divisi.custom') }}" class="mt-3 md:mt-0 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Buat Divisi
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($divisions as $division)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $division->name }}</h2>
                        <p class="text-xs text-gray-400 font-mono">{{ $division->code }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold
                        {{ $division->status == 'aktif' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ ucfirst($division->status) }}
                    </span>
                </div>

                @if($division->description)
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 line-clamp-2">{{ $division->description }}</p>
                @endif

                <div class="mt-3 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span><i class="fas fa-users mr-1"></i> <span id="member-count-{{ $division->id }}">{{ $division->members->count() }}</span> anggota</span>
                    <span><i class="fas fa-user-cog mr-1"></i> {{ $division->creator->name ?? 'Sistem' }}</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('owner.divisi.anggota', ['division_id' => $division->id]) }}" class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg text-xs hover:bg-blue-200 transition">
                        <i class="fas fa-info-circle mr-1"></i> Detail
                    </a>
                    <a href="{{ route('owner.divisi.akses', ['division_id' => $division->id]) }}" class="px-3 py-1 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg text-xs hover:bg-red-200 transition">
                        <i class="fas fa-key mr-1"></i> Hak Akses
                    </a>
                    <a href="{{ route('owner.divisi.anggota', ['division_id' => $division->id]) }}" class="px-3 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-xs hover:bg-green-200 transition">
                        <i class="fas fa-users mr-1"></i> Anggota
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fas fa-sitemap text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada divisi.</p>
                <a href="{{ route('owner.divisi.custom') }}" class="text-rose-600 hover:underline mt-2 inline-block">Buat divisi sekarang</a>
            </div>
        @endforelse
    </div>
</div>

<script>
    function updateMemberCounts() {
        const divisionIds = @json($divisions->pluck('id'));
        divisionIds.forEach(id => {
            fetch(`/owner/divisi/${id}/members-count`)
                .then(res => res.json())
                .then(data => {
                    const el = document.getElementById(`member-count-${id}`);
                    if (el) el.textContent = data.count;
                })
                .catch(err => console.error('Gagal update count:', err));
        });
    }
    setInterval(updateMemberCounts, 10000);
</script>
@endsection