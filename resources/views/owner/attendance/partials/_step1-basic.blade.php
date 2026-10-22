<div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-step-enter">

    <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-cyan-50 to-teal-50 dark:from-cyan-900/20 dark:to-teal-900/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 dark:text-white">Informasi Dasar</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Judul, tipe kegiatan, dan mode kehadiran</p>
            </div>
        </div>
    </div>

    <div class="p-5 md:p-6 space-y-6">

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Judul Sesi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <i class="fas fa-heading absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="form.title" maxlength="150"
                       placeholder="Contoh: Rapat Koordinasi Mingguan"
                       class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all placeholder-gray-400">
            </div>
            <p class="text-xs text-gray-400 mt-1 text-right">
                <span x-text="form.title.length"></span>/150
            </p>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
            <textarea x-model="form.description" rows="3" maxlength="500"
                      placeholder="Jelaskan tujuan sesi ini..."
                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all resize-none placeholder-gray-400"></textarea>
        </div>

        {{-- Tipe Kegiatan --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                Tipe Kegiatan <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <template x-for="t in activityTypes" :key="t.id">
                    <label class="choice-card" :class="{ 'selected': form.attendance_type === t.id }">
                        <input type="radio" class="sr-only" :value="t.id" x-model="form.attendance_type">
                        <i class="fas choice-card-icon" :class="t.icon"></i>
                        <span class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300" x-text="t.label"></span>
                    </label>
                </template>
            </div>
        </div>

        {{-- Mode Kehadiran --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                Mode Kehadiran <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <template x-for="m in attendanceModes" :key="m.id">
                    <label class="choice-card" :class="{ 'selected': form.attendance_mode === m.id }">
                        <input type="radio" class="sr-only" :value="m.id" x-model="form.attendance_mode">
                        <i class="fas choice-card-icon" :class="m.icon"></i>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 text-sm" x-text="m.label"></span>
                        <span class="text-[10px] text-gray-400 leading-tight" x-text="m.desc"></span>
                    </label>
                </template>
            </div>
        </div>

        {{-- Jadwal & Ruangan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Jadwal Terkait <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                </label>
                <select x-model="form.schedule_id" @change="onScheduleChange()"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
                    <option value="">— Tidak terkait jadwal —</option>
                    <template x-for="s in schedules" :key="s.id">
                        <option :value="s.id" x-text="s.title + ' (' + (s.start_datetime || '') + ')'"></option>
                    </template>
                </select>
                <p x-show="selectedSchedule" class="text-xs text-cyan-600 dark:text-cyan-400 mt-2 flex items-center gap-1">
                    <i class="fas fa-link"></i> Waktu absensi akan tersinkron otomatis.
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ruangan</label>
                <select x-model="form.room_id"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all">
                    <option value="">— Pilih ruangan —</option>
                    <template x-for="r in rooms" :key="r.id">
                        <option :value="r.id" x-text="r.name + (r.building ? ' - ' + r.building : '')"></option>
                    </template>
                </select>
                <div x-show="selectedRoom" class="mt-2 p-2 rounded-lg bg-cyan-50 dark:bg-cyan-900/20 text-xs text-cyan-700 dark:text-cyan-300 flex items-center gap-2">
                    <i class="fas fa-door-open"></i>
                    <span x-text="'Kapasitas: ' + (selectedRoom?.capacity_normal || '-') + ' orang'"></span>
                </div>
            </div>
        </div>

        {{-- Shift --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Nama Shift <span class="text-gray-400 font-normal text-xs">(opsional)</span>
            </label>
            <input type="text" x-model="form.shift_name" maxlength="100"
                   placeholder="Contoh: Shift Pagi / Shift 1"
                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-4 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all placeholder-gray-400">
        </div>
    </div>
</div>