@extends('layouts.roles.ketua') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Pilih Organisasi')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Pilih Organisasi Anda
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Anda terdaftar di beberapa organisasi. Pilih salah satu untuk mulai mengelola.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($organizations as $org)
                <form method="POST" action="{{ route('organization.switch', $org) }}" class="h-full">
                    @csrf
                    <button type="submit" class="w-full h-full text-left p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 hover:border-indigo-500 dark:hover:border-indigo-400 hover:shadow-lg transition-all duration-200 group relative overflow-hidden">
                        
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/20 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="relative z-10 flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-building text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $org->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide font-semibold">
                                        {{ ucfirst($org->type) }} · {{ $org->short_name }}
                                    </p>
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-gray-300 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all"></i>
                        </div>

                        <div class="relative z-10 mt-6 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ ucfirst($org->pivot->access_role) }}
                            </span>
                            @if($org->pivot->position_label)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $org->pivot->position_label }}
                                </span>
                            @endif
                        </div>
                    </button>
                </form>
            @empty
                <div class="col-span-full p-12 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Organisasi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum terdaftar sebagai anggota di organisasi mana pun.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection