@extends('layouts.roles.owner')

@section('title', 'Detail Pengumpulan — ' . ($submission->item->title ?? 'Submission'))

@section('content')
@php
    $item = $submission->item;
    $user = $submission->user;
    $deadline = $item->deadline_at ?? null;
    $isLate = $submission->is_late;
    $hasGraded = $submission->reviewed_at !== null;

    $statusMeta = match ($submission->status) {
        'pending_review' => ['label' => 'Menunggu Review', 'color' => 'amber', 'icon' => 'fa-hourglass-half'],
        'completed', 'done' => ['label' => 'Selesai', 'color' => 'emerald', 'icon' => 'fa-circle-check'],
        'revision' => ['label' => 'Revisi', 'color' => 'rose', 'icon' => 'fa-rotate-left'],
        'late' => ['label' => 'Terlambat', 'color' => 'rose', 'icon' => 'fa-clock'],
        default => ['label' => 'Draft', 'color' => 'slate', 'icon' => 'fa-file-pen'],
    };
@endphp

<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-5">

    {{-- HEADER --}}
    <div>
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap mb-2">
            <a href="{{ route('owner.lms.monitoring') }}" class="hover:text-violet-600">
                <i class="fas fa-chart-line"></i> Monitoring
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <a href="{{ route('owner.lms.submissions.index') }}" class="hover:text-violet-600">Pengumpulan</a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Detail</span>
        </nav>

        <div class="flex items-start justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
                    {{ $user->name ?? 'User dihapus' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Mengumpulkan untuk: <b class="text-violet-600 dark:text-violet-400">{{ $item->title ?? '-' }}</b>
                </p>
            </div>
            <a href="{{ route('owner.lms.items.show', $item->id ?? 0) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition">
                <i class="fas fa-external-link-alt"></i> Lihat Tugas
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 text-emerald-700 dark:text-emerald-300 text-sm">
            <i class="fas fa-circle-check mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KIRI: submission content --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- META --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400">Status</p>
                        <p class="mt-1 font-black text-sm text-{{ $statusMeta['color'] }}-600 dark:text-{{ $statusMeta['color'] }}-400">
                            <i class="fas {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['label'] }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400">Dikumpulkan</p>
                        <p class="mt-1 font-bold text-sm text-gray-800 dark:text-gray-200">
                            {{ optional($submission->submitted_at)->isoFormat('D MMM Y · HH:mm') ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400">Deadline</p>
                        <p class="mt-1 font-bold text-sm {{ $isLate ? 'text-rose-600 dark:text-rose-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ optional($deadline)->isoFormat('D MMM Y · HH:mm') ?? 'Tanpa batas' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400">Attempt</p>
                        <p class="mt-1 font-bold text-sm text-gray-800 dark:text-gray-200">
                            #{{ $submission->is_resubmit ? '2+' : '1' }}
                        </p>
                    </div>
                </div>
                @if($isLate)
                    <div class="mt-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40">
                        <p class="text-xs font-black text-rose-700 dark:text-rose-300">
                            <i class="fas fa-exclamation-triangle"></i> Dikumpulkan melewati deadline
                        </p>
                    </div>
                @endif
            </div>

            {{-- ANSWER --}}
            @if($submission->answer_text)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-[10px] uppercase font-black text-gray-400 mb-2">Jawaban Teks</p>
                    <div class="text-sm leading-7 text-gray-700 dark:text-gray-300 whitespace-pre-line break-words">
                        {{ $submission->answer_text }}
                    </div>
                </div>
            @endif

            {{-- LINK --}}
            @if($submission->answer_link)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-[10px] uppercase font-black text-gray-400 mb-2">Link Jawaban</p>
                    <a href="{{ $submission->answer_link }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline break-all">
                        <i class="fas fa-external-link-alt"></i> {{ $submission->answer_link }}
                    </a>
                </div>
            @endif

            {{-- NOTE --}}
            @if($submission->note)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-[10px] uppercase font-black text-gray-400 mb-2">Catatan Pengumpul</p>
                    <p class="text-sm italic text-gray-600 dark:text-gray-400">{{ $submission->note }}</p>
                </div>
            @endif

            {{-- FILES --}}
            @if($submission->files && $submission->files->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-[10px] uppercase font-black text-gray-400 mb-3">
                        Lampiran ({{ $submission->files->count() }})
                    </p>
                    <div class="space-y-2">
                        @foreach($submission->files as $f)
                            <a href="{{ asset('storage/' . $f->path) }}" target="_blank" rel="noopener"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-violet-400 transition">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ $f->original_name }}</p>
                                    <p class="text-[10px] text-gray-500">
                                        {{ number_format($f->size / 1024, 1) }} KB · {{ $f->mime_type }}
                                    </p>
                                </div>
                                <i class="fas fa-download text-gray-400"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- KANAN: review panel --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- RIWAYAT REVIEW --}}
            @if($hasGraded)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-700/40 p-5">
                    <p class="text-[10px] uppercase font-black text-emerald-600 mb-3">
                        <i class="fas fa-check-circle"></i> Sudah Direview
                    </p>
                    <div class="space-y-3">
                        <div>
                            <p class="text-[10px] uppercase font-black text-gray-400">Reviewer</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                {{ $submission->reviewer->name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-black text-gray-400">Waktu</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                {{ $submission->reviewed_at?->isoFormat('D MMM Y · HH:mm') }}
                            </p>
                        </div>
                        @if($submission->score !== null)
                            <div>
                                <p class="text-[10px] uppercase font-black text-gray-400">Nilai</p>
                                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                    {{ $submission->score }}<span class="text-sm opacity-60">/100</span>
                                </p>
                            </div>
                        @endif
                        @if($submission->feedback)
                            <div>
                                <p class="text-[10px] uppercase font-black text-gray-400">Feedback</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $submission->feedback }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- FORM REVIEW --}}
            <form method="POST" action="{{ route('owner.lms.submissions.review', $submission->id) }}"
                  class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                @csrf

                <p class="text-[10px] uppercase font-black text-gray-400">
                    {{ $hasGraded ? 'Review Ulang' : 'Review' }}
                </p>

                <div>
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Nilai (0-100)
                    </label>
                    <input type="number" name="score" min="0" max="100"
                           value="{{ old('score', $submission->score) }}"
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                        Feedback / Catatan
                    </label>
                    <textarea name="feedback" rows="4" maxlength="2000"
                              placeholder="Tulis feedback untuk mahasiswa..."
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-violet-500/30 resize-y">{{ old('feedback', $submission->feedback) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <button type="submit" name="action" value="revision"
                            class="px-4 py-2.5 rounded-xl bg-rose-500 text-white text-xs font-black hover:bg-rose-600 transition
                                   inline-flex items-center justify-center gap-1">
                        <i class="fas fa-rotate-left"></i> Minta Revisi
                    </button>
                    <button type="submit" name="action" value="approve"
                            class="px-4 py-2.5 rounded-xl bg-emerald-500 text-white text-xs font-black hover:bg-emerald-600 transition
                                   inline-flex items-center justify-center gap-1">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection