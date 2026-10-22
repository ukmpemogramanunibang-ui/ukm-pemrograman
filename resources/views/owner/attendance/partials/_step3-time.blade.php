<div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-step-enter">

    <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 dark:text-white">Waktu & Keamanan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pengaturan jadwal dan kebijakan akses</p>
            </div>
        </div>
    </div>

    <div class="p-5 md:p-6 space-y-6">

        {{-- Notifikasi sinkron --}}
        <div x-show="selectedSchedule" class="p-4 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800 flex items-start gap-3">
            <i class="fas fa-sync-alt text-cyan-500 mt-0.5"></i>
            <div class="text-xs text-cyan-700 dark:text-cyan-300">
                <p class="font-semibold mb-0.5">Waktu tersinkron otomatis dari jadwal</p>
                <p>Mengubah jadwal akan memperbarui waktu di bawah. Anda bisa ubah manual jika perlu.</p>
            </div>
        </div>

        {{-- Grid waktu --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Mulai Dijadwalkan <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" x-model="form.scheduled_start_at"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Selesai Dijadwalkan <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" x-model="form.scheduled_end_at"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Absensi Dibuka <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" x-model="form.attendance_open_at"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Absensi Ditutup <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" x-model="form.attendance_close_at"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
            </div>
        </div>

        {{-- Kebijakan --}}
        <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/50">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                <i class="fas fa-shield-alt text-emerald-500"></i> Kebijakan Absensi
            </p>
            <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1.5">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                    <span>Anggota di luar target tetap boleh absen, tetapi <b>poin tidak dihitung</b>.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                    <span>Absen di luar jadwal tetap tercatat sebagai <b>kehadiran biasa</b>.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                    <span>Sistem memvalidasi role, kelompok/divisi/generasi, waktu, dan metode secara otomatis.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                    <span>Absen berulang di sesi yang sama akan tercatat sebagai <b>validasi ulang</b>, bukan poin baru.</span>
                </li>
            </ul>
        </div>
    </div>
</div>