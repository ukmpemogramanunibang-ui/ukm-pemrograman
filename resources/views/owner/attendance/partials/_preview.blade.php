<aside class="hidden lg:block lg:col-span-1">
    <div class="sticky top-32 space-y-4">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-cyan-50 to-teal-50 dark:from-cyan-900/20 dark:to-teal-900/20 flex items-center gap-2">
                <i class="fas fa-eye text-cyan-500"></i>
                <h3 class="font-bold text-sm text-gray-800 dark:text-white">Preview Live</h3>
                <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold">
                    AUTO
                </span>
            </div>

            <div class="p-5 space-y-4 preview-scroll max-h-[calc(100vh-14rem)] overflow-y-auto">

                <div class="text-center pb-3 border-b border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-cyan-500/30 mb-2">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 dark:text-white text-sm leading-tight break-words"
                        x-text="form.title || 'Judul sesi belum diisi'"></h4>
                    <p class="text-[11px] text-gray-400 mt-1 break-words"
                       x-text="form.description || 'Tanpa deskripsi'"></p>
                </div>

                <dl class="text-xs space-y-2">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Tipe</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right capitalize" x-text="form.attendance_type || '—'"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Mode</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right capitalize" x-text="form.attendance_mode || '—'"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Jadwal</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right truncate max-w-[150px]"
                            x-text="selectedSchedule ? selectedSchedule.title : '—'"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Ruangan</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right truncate max-w-[150px]"
                            x-text="selectedRoom ? selectedRoom.name : '—'"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Mulai</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right text-[11px]" x-text="formatDate(form.scheduled_start_at)"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Selesai</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right text-[11px]" x-text="formatDate(form.scheduled_end_at)"></dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-500 dark:text-gray-400">Shift</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200 text-right" x-text="form.shift_name || '—'"></dd>
                    </div>
                </dl>

                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">Target Role</p>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="r in form.target_roles" :key="r">
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-medium capitalize"
                                  x-text="r.replace('_', ' ')"></span>
                        </template>
                        <template x-if="form.target_roles.length === 0">
                            <span class="text-[10px] text-gray-400 italic">Semua role</span>
                        </template>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">Metode</p>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="m in form.allowed_methods" :key="m">
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 font-bold uppercase"
                                  x-text="m"></span>
                        </template>
                        <template x-if="form.allowed_methods.length === 0">
                            <span class="text-[10px] text-red-400 italic">Belum dipilih</span>
                        </template>
                    </div>
                </div>

                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] uppercase text-gray-500 dark:text-gray-400 font-semibold mb-2">Ringkasan Target</p>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-lg font-bold text-purple-600 dark:text-purple-400" x-text="form.target_groups.length"></p>
                            <p class="text-[10px] text-gray-500">Kelompok</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-pink-600 dark:text-pink-400" x-text="form.target_divisions.length"></p>
                            <p class="text-[10px] text-gray-500">Divisi</p>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400" x-text="form.target_generations.length"></p>
                            <p class="text-[10px] text-gray-500">Generasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800">
            <div class="flex items-start gap-2">
                <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                <div class="text-xs text-amber-800 dark:text-amber-200">
                    <p class="font-semibold mb-1">Tips</p>
                    <ul class="space-y-1 list-disc list-inside">
                        <li>Pilih jadwal dulu → waktu terisi otomatis.</li>
                        <li>Gunakan <b>Pilih Semua</b> untuk target cepat.</li>
                        <li>Kombinasi Manual + QR sangat dianjurkan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside>