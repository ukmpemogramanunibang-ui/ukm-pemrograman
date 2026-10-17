@extends('layouts.role.anggota')
@section('title', 'Portofolio')
@section('breadcrumb', 'Portofolio')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="glass-card rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">💼 Portofolio</h2>
            <button class="px-4 py-2 bg-cyan text-white rounded-lg hover:bg-cyan/90 transition text-sm">+ Tambah Karya</button>
        </div>

        @php
            // Asumsi ada model Portofolio
            $portofolios = \App\Models\Portofolio::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(6);
        @endphp

        @if($portofolios->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($portofolios as $p)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-lg transition">
                        <h3 class="font-semibold text-gray-800 dark:text-white">{{ $p->title }}</h3>
                        <p class="text-sm text-gray-500">{{ Str::limit($p->description, 80) }}</p>
                        <a href="{{ $p->link }}" target="_blank" class="text-cyan text-xs hover:underline">Lihat Proyek</a>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $portofolios->links() }}</div>
        @else
            <p class="text-sm text-gray-400">Belum ada portofolio. Tambahkan karya pertamamu!</p>
        @endif
    </div>
</div>
@endsection