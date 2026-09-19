@extends('layouts.roles.owner')

@section('title', 'Buat Sesi Absensi – Owner')
@section('page-title', 'Buat Sesi Absensi Baru')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    /* ── Progress bar ── */
    .wizard-progress-bar {
        transition: width .5s cubic-bezier(.4,0,.2,1);
        background: linear-gradient(90deg, #06b6d4 0%, #14b8a6 50%, #10b981 100%);
        box-shadow: 0 0 12px rgba(20,184,166,.45);
    }

    /* ── Choice card ── */
    .choice-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: 1rem;
        border-radius: .75rem;
        border: 2px solid rgb(229 231 235);
        background: white;
        cursor: pointer;
        transition: all .2s ease;
        text-align: center;
        min-height: 108px;
        user-select: none;
    }
    .dark .choice-card {
        border-color: rgb(55 65 81);
        background: rgb(31 41 55);
    }
    .choice-card:hover {
        border-color: #22d3ee;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(34,211,238,.12);
    }
    .choice-card.selected {
        border-color: #06b6d4;
        background: linear-gradient(135deg, rgba(6,182,212,.06), rgba(20,184,166,.06));
        box-shadow: 0 8px 24px rgba(6,182,212,.18), 0 0 0 3px rgba(6,182,212,.15);
    }
    .dark .choice-card.selected {
        background: linear-gradient(135deg, rgba(6,182,212,.15), rgba(20,184,166,.15));
        border-color: #22d3ee;
    }
    .choice-card.selected .choice-card-icon { color: #06b6d4; }
    .choice-card-icon {
        font-size: 1.75rem;
        color: rgb(156 163 175);
        transition: color .2s ease, transform .2s ease;
    }
    .choice-card.selected .choice-card-icon {
        transform: scale(1.1);
    }

    /* ── Chip check ── */
    .chip-check {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .75rem;
        border-radius: .5rem;
        border: 2px solid rgb(229 231 235);
        background: white;
        cursor: pointer;
        font-size: .8125rem;
        font-weight: 500;
        transition: all .15s ease;
        user-select: none;
        color: rgb(55 65 81);
    }
    .dark .chip-check {
        border-color: rgb(55 65 81);
        background: rgb(31 41 55);
        color: rgb(209 213 219);
    }
    .chip-check:hover {
        border-color: #22d3ee;
        box-shadow: 0 2px 8px rgba(34,211,238,.12);
    }
    .chip-check.selected {
        border-color: #06b6d4;
        background: rgb(236 254 255);
        color: #0e7490;
        box-shadow: 0 2px 8px rgba(6,182,212,.15);
    }
    .dark .chip-check.selected {
        background: rgba(6,182,212,.2);
        color: #67e8f9;
        border-color: #22d3ee;
    }
    .chip-check input { position: absolute; opacity: 0; pointer-events: none; }

    /* ── Step indicator ── */
    .step-dot {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .8125rem;
        border: 2px solid;
        transition: all .3s ease;
        cursor: pointer;
    }
    .step-dot.active {
        background: linear-gradient(135deg, #06b6d4, #14b8a6);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 16px rgba(6,182,212,.45);
        transform: scale(1.1);
    }
    .step-dot.done {
        background: #10b981;
        color: white;
        border-color: transparent;
        box-shadow: 0 2px 10px rgba(16,185,129,.35);
    }
    .step-dot.pending {
        background: white;
        color: rgb(156 163 175);
        border-color: rgb(209 213 219);
    }
    .dark .step-dot.pending {
        background: rgb(31 41 55);
        border-color: rgb(75 85 99);
    }
    .step-dot.pending:hover {
        border-color: #22d3ee;
    }

    /* ── Smooth scroll ── */
    html { scroll-behavior: smooth; }

    /* ── Custom scrollbar ── */
    .preview-scroll::-webkit-scrollbar { width: 6px; }
    .preview-scroll::-webkit-scrollbar-track { background: transparent; }
    .preview-scroll::-webkit-scrollbar-thumb {
        background: rgb(203 213 225);
        border-radius: 9999px;
    }
    .dark .preview-scroll::-webkit-scrollbar-thumb { background: rgb(71 85 105); }

    /* ── Action bar mobile ── */
    .action-bar {
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        background: rgba(255,255,255,.92);
    }
    .dark .action-bar {
        background: rgba(17,24,39,.92);
    }

    /* ── Fade-in animation ── */
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-step-enter {
        animation: fadeSlideUp .35s cubic-bezier(.4,0,.2,1);
    }

    /* ── Pulse ring ── */
    @keyframes pulseRing {
        0%   { box-shadow: 0 0 0 0 rgba(6,182,212,.4); }
        70%  { box-shadow: 0 0 0 10px rgba(6,182,212,0); }
        100% { box-shadow: 0 0 0 0 rgba(6,182,212,0); }
    }
    .pulse-ring { animation: pulseRing 2s infinite; }

    /* ── Prevent horizontal overflow saat modal ── */
    body { overflow-x: hidden; }
</style>
@endpush

@section('content')
<div x-data="attendanceWizard()" x-cloak class="min-h-screen pb-28 lg:pb-6">

    {{-- ═══════════════════════════════════════════ --}}
    {{-- HEADER + STEPPER                              --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-200 dark:border-gray-700 -mx-4 md:-mx-6 px-4 md:px-6 pt-3 pb-4 shadow-sm">
        <div class="max-w-7xl mx-auto">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-3 overflow-x-auto">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-600 whitespace-nowrap transition">
                    <i class="fas fa-home text-[10px]"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.attendance.index') }}" class="hover:text-cyan-600 whitespace-nowrap transition">
                    Absensi
                </a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-200 font-semibold whitespace-nowrap">Buat Sesi Baru</span>
            </nav>

            {{-- Judul + Stepper --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2 truncate">
                        <i class="fas fa-clipboard-check text-cyan-500"></i>
                        <span class="truncate">Buat Sesi Absensi Baru</span>
                    </h1>
                    <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Tahap <span class="font-semibold text-cyan-600 dark:text-cyan-400" x-text="step"></span>
                        dari <span x-text="totalSteps"></span> —
                        <span x-text="stepTitle"></span>
                    </p>
                </div>

                {{-- Step dots (desktop) --}}
                <div class="hidden md:flex items-center gap-3">
                    <template x-for="n in totalSteps" :key="n">
                        <div class="flex items-center">
                            <button @click="goToStep(n)" type="button"
                                    :class="{
                                        'step-dot active': step === n,
                                        'step-dot done': step > n,
                                        'step-dot pending': step < n
                                    }"
                                    :title="stepLabel(n)">
                                <template x-if="step > n">
                                    <i class="fas fa-check text-xs"></i>
                                </template>
                                <template x-if="step <= n">
                                    <span x-text="n"></span>
                                </template>
                            </button>
                            <div x-show="n < totalSteps"
                                 class="w-8 lg:w-16 h-1 mx-1 rounded-full transition-colors duration-500"
                                 :class="step > n ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="mt-3 h-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                <div class="wizard-progress-bar h-full rounded-full"
                     :style="`width: ${(step / totalSteps) * 100}%`"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- ERROR VALIDASI                               --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($errors->any())
        <div class="max-w-7xl mx-auto mt-4 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl animate-step-enter">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-lg mt-0.5"></i>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-red-700 dark:text-red-300 text-sm mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside text-xs md:text-sm text-red-600 dark:text-red-300 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.closest('div').parentElement.remove()" class="text-red-400 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- FORM WRAPPER                                 --}}
    {{-- ═══════════════════════════════════════════ --}}
    <form action="{{ route('owner.attendance.store') }}" method="POST"
          x-ref="form" @submit.prevent="submitForm" class="max-w-7xl mx-auto mt-6">
        @csrf

        {{-- Hidden fields — semua Alpine state di-mirror ke form --}}
        <input type="hidden" name="title"              :value="form.title">
        <input type="hidden" name="description"        :value="form.description">
        <input type="hidden" name="attendance_type"    :value="form.attendance_type">
        <input type="hidden" name="attendance_mode"    :value="form.attendance_mode">
        <input type="hidden" name="schedule_id"        :value="form.schedule_id">
        <input type="hidden" name="room_id"            :value="form.room_id">
        <input type="hidden" name="shift_name"         :value="form.shift_name">
        <input type="hidden" name="target_type"        :value="form.target_type">
        <input type="hidden" name="scheduled_start_at" :value="form.scheduled_start_at">
        <input type="hidden" name="scheduled_end_at"   :value="form.scheduled_end_at">
        <input type="hidden" name="attendance_open_at" :value="form.attendance_open_at">
        <input type="hidden" name="attendance_close_at":value="form.attendance_close_at">

        <template x-for="m in form.allowed_methods" :key="'m'+m">
            <input type="hidden" name="allowed_methods[]" :value="m">
        </template>
        <template x-for="r in form.target_roles" :key="'r'+r">
            <input type="hidden" name="target_roles[]" :value="r">
        </template>
        <template x-for="g in form.target_groups" :key="'g'+g">
            <input type="hidden" name="target_groups[]" :value="g">
        </template>
        <template x-for="d in form.target_divisions" :key="'d'+d">
            <input type="hidden" name="target_divisions[]" :value="d">
        </template>
        <template x-for="g in form.target_generations" :key="'gen'+g">
            <input type="hidden" name="target_generations[]" :value="g">
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ═══════════════════════════════════ --}}
            {{-- KOLOM KIRI — STEP CONTENT            --}}
            {{-- ═══════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-6">

                @include('owner.attendance.partials._step1-basic')
                @include('owner.attendance.partials._step2-target')
                @include('owner.attendance.partials._step3-time')

                {{-- Navigasi desktop --}}
                <div class="hidden lg:flex items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="prevStep()" x-show="step > 1"
                            class="px-5 py-2.5 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-100 dark:hover:bg-gray-800 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    <div class="flex-1"></div>
                    <a href="{{ route('owner.attendance.index') }}"
                       class="px-5 py-2.5 rounded-xl text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-sm transition">
                        Batal
                    </a>
                    <button type="button" @click="nextStep()" x-show="step < totalSteps"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-600 hover:to-teal-600 text-white font-semibold shadow-lg shadow-cyan-500/30 transition flex items-center gap-2">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" x-show="step === totalSteps" @click="openPreview()"
                            :disabled="isSubmitting"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold shadow-lg shadow-emerald-500/30 transition flex items-center gap-2 disabled:opacity-60">
                        <i class="fas fa-check-circle"></i> Validasi &amp; Simpan
                    </button>
                </div>
            </div>

            {{-- ═══════════════════════════════════ --}}
            {{-- KOLOM KANAN — PREVIEW LIVE           --}}
            {{-- ═══════════════════════════════════ --}}
            @include('owner.attendance.partials._preview')
        </div>

        {{-- ═══════════════════════════════════ --}}
        {{-- ACTION BAR MOBILE (STICKY BOTTOM)   --}}
        {{-- ═══════════════════════════════════ --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 action-bar border-t border-gray-200 dark:border-gray-700 shadow-2xl px-4 py-3 flex items-center gap-2">
            <button type="button" @click="prevStep()" x-show="step > 1"
                    class="px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium flex-shrink-0">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button type="button" @click="nextStep()" x-show="step < totalSteps"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-500 text-white font-semibold shadow-lg flex items-center justify-center gap-2">
                Selanjutnya <i class="fas fa-arrow-right"></i>
            </button>
            <button type="button" x-show="step === totalSteps" @click="openPreview()"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Simpan
            </button>
            <button type="button" @click="showPreviewMobile = true"
                    class="px-4 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 flex-shrink-0"
                    title="Preview">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </form>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL PREVIEW MOBILE                         --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="showPreviewMobile" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="lg:hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end"
         @click.self="showPreviewMobile = false">
        <div class="bg-white dark:bg-gray-900 w-full max-h-[90vh] rounded-t-2xl overflow-y-auto preview-scroll"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0">
            <div class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between z-10">
                <h3 class="font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-eye text-cyan-500 mr-1"></i> Preview Sesi
                </h3>
                <button @click="showPreviewMobile = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4" x-html="previewHtml()"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI SUBMIT                      --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="showConfirm" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="showConfirm = false">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-6"
             @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 flex items-center justify-center mb-4 pulse-ring">
                    <i class="fas fa-check-circle text-3xl text-emerald-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Konfirmasi Sesi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    Pastikan semua data sudah benar. Sesi absensi akan dibuat dan siap digunakan.
                </p>

                {{-- Ringkasan singkat --}}
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900/50 p-3 mb-5 text-left text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Judul:</span>
                        <span class="font-semibold text-gray-800 dark:text-white truncate ml-2 max-w-[60%]" x-text="form.title || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipe:</span>
                        <span class="font-semibold capitalize" x-text="form.attendance_type || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mode:</span>
                        <span class="font-semibold capitalize" x-text="form.attendance_mode || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode:</span>
                        <span class="font-semibold uppercase" x-text="form.allowed_methods.join(', ') || '—'"></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="showConfirm = false" type="button"
                            class="flex-1 px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Periksa Lagi
                    </button>
                    <button @click="submitForm()" type="button" :disabled="isSubmitting"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold shadow-lg disabled:opacity-60 flex items-center justify-center gap-2 hover:from-emerald-600 hover:to-teal-600 transition">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Sekarang'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/attendance-wizard.js') }}"></script>
<script>
    // Bootstrap data dari Laravel → Alpine
    window.__ATTENDANCE_WIZARD_DATA__ = {
        old: {
            title:               @json(old('title', $prefillSchedule->title ?? '')),
            description:         @json(old('description', '')),
            attendance_type:     @json(old('attendance_type', 'regular')),
            attendance_mode:     @json(old('attendance_mode', 'offline')),
            schedule_id:         @json(old('schedule_id', $prefillSchedule->id ?? '')),
            room_id:             @json(old('room_id', $prefillSchedule->room_id ?? '')),
            shift_name:          @json(old('shift_name', '')),
            target_type:         @json(old('target_type', 'all')),
            target_roles:        @json(old('target_roles', [])),
            target_groups:       @json(old('target_groups', [])),
            target_divisions:    @json(old('target_divisions', [])),
            target_generations:  @json(old('target_generations', [])),
            allowed_methods:     @json(old('allowed_methods', ['manual'])),
            scheduled_start_at:  @json(old('scheduled_start_at', $prefillSchedule->start_datetime ?? '')),
            scheduled_end_at:    @json(old('scheduled_end_at', $prefillSchedule->end_datetime ?? '')),
            attendance_open_at:  @json(old('attendance_open_at', $prefillSchedule->start_datetime ?? '')),
            attendance_close_at: @json(old('attendance_close_at', $prefillSchedule->end_datetime ?? '')),
        },
        schedules:   @json($schedules   ?? []),
        rooms:       @json($rooms       ?? []),
        groups:      @json($groups      ?? []),
        divisions:   @json($divisions   ?? []),
        generations: @json($generations ?? []),
    };
</script>
@endpush