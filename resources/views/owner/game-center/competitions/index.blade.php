@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        <div class="rounded-3xl overflow-hidden bg-gradient-to-br from-amber-500 via-orange-600 to-red-600 text-white">
            <div class="p-6 md:p-8">
                <span class="text-xs uppercase tracking-widest text-white/60">Tournament System</span>
                <h1 class="text-3xl md:text-4xl font-black mt-2">Kompetisi UNICODE</h1>
                <p class="mt-2 text-white/75 max-w-2xl">
                    Kelola turnamen, bracket, peserta, hadiah, jadwal pertandingan, sistem poin dan juara secara terintegrasi.
                </p>
                <a href="{{ route('owner.game.competitions.create') }}" class="mt-6 inline-block px-5 py-3 rounded-xl bg-white text-orange-600 font-black hover:bg-orange-50">
    + Buat Kompetisi
</a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Kompetisi Aktif</p>
                <h3 class="text-2xl font-black mt-2">{{ $competitions->whereIn('status', ['registration', 'live'])->count() }}</h3>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Total Peserta</p>
                <h3 class="text-2xl font-black mt-2">{{ $competitions->sum('current_participants') ?: 0 }}</h3>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Total Hadiah</p>
                <h3 class="text-2xl font-black mt-2">Rp {{ number_format($competitions->sum('prize_pool') ?: 0, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
                <p class="text-xs text-slate-500">Kompetisi Selesai</p>
                <h3 class="text-2xl font-black mt-2">{{ $competitions->where('status', 'finished')->count() }}</h3>
            </div>
        </div>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($competitions as $competition)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-indigo-600 to-purple-700 p-5 flex items-start justify-between">
                    <div class="text-4xl">🏆</div>
                    <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur text-white text-[10px] font-bold">
                        {{ strtoupper($competition->status) }}
                    </span>
                </div>

                <div class="p-5">
                    <h2 class="font-black text-lg">{{ $competition->name }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $competition->game->name ?? 'Multiple Games' }}</p>

                    <div class="flex justify-between mt-5 text-xs">
                        <span class="text-slate-500">Peserta</span>
                        <span class="font-bold">{{ $competition->current_participants }} / {{ $competition->max_participants }}</span>
                    </div>

                    <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full mt-2 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-red-500" style="width: {{ $competition->max_participants > 0 ? ($competition->current_participants / $competition->max_participants) * 100 : 0 }}%"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-5">
<a href="{{ route('owner.game.competitions.show', ['competition' => $competition->getKey()]) }}" class="py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-sm font-bold text-center hover:bg-slate-200 dark:hover:bg-slate-700">
                            Detail
                        </a>
<a href="{{ route('owner.game.competitions.edit', ['competition' => $competition->getKey()]) }}" class="py-2.5 rounded-xl bg-orange-500 text-white text-sm font-bold text-center hover:bg-orange-600">
                            Kelola
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-slate-500">
                <div class="text-6xl mb-4">🏆</div>
                <p>Belum ada kompetisi. Buat kompetisi pertama!</p>
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $competitions->links() }}
        </div>
    </div>
</div>
@endsection