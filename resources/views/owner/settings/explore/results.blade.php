@extends('layouts.roles.owner')

@section('title', 'Hasil Pencarian – Explorer')

@section('content')
<div class="p-4 md:p-6">

    <div class="mb-6 flex items-center justify-between flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Hasil Pencarian
            </h1>
            <p class="text-gray-500 dark:text-gray-400">
                Ditemukan <strong>{{ $users->count() }}</strong> akun untuk keyword "{{ $keyword }}"
            </p>
        </div>
        <a href="{{ route('owner.settings.explore.index') }}"
           class="text-indigo-600 hover:underline text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

        @forelse($users as $user)
            <div class="bg-white dark:bg-gray-800
                        rounded-2xl border
                        border-gray-200 dark:border-gray-700
                        shadow-sm p-5
                        hover:shadow-md transition">

                <div class="flex items-center gap-4">

                    <div class="w-14 h-14 rounded-full
                                bg-indigo-100 dark:bg-indigo-900/40
                                flex items-center justify-center
                                text-2xl">
                        👤
                    </div>

                    <div>
                        <h2 class="font-bold text-gray-900 dark:text-white">
                            {{ $user->name }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            NIM: {{ $user->nim ?? '-' }}
                        </p>
                        <p class="text-sm font-medium
                            @if($user->level_id == 3) text-blue-600
                            @elseif($user->level_id == 4) text-green-600
                            @elseif($user->level_id == 5) text-gray-600
                            @else text-gray-400 @endif">
                            {{ $user->level->name ?? 'Unknown' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('owner.settings.explore.verify', ['user_id' => $user->id]) }}"
                   class="mt-5 block text-center
                          rounded-xl bg-indigo-600
                          hover:bg-indigo-700
                          text-white py-3 font-semibold
                          transition duration-200">
                    <i class="fas fa-lock mr-2"></i> Verifikasi & Jelajah
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fas fa-user-slash text-4xl mb-3 block opacity-30"></i>
                <p class="text-lg">Akun tidak ditemukan.</p>
                <a href="{{ route('owner.settings.explore.index') }}" class="text-indigo-600 hover:underline mt-2 inline-block">
                    Coba cari lagi
                </a>
            </div>
        @endforelse
<form action="{{ route('owner.settings.explore.verify') }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <button type="submit">Lanjutkan Jelajah</button>
</form>
    </div>
</div>
@endsection 