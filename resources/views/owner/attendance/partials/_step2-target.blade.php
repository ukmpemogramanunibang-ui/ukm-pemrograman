<div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-step-enter">

    <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fas fa-bullseye"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 dark:text-white">Target & Metode</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Siapa yang boleh absen dan bagaimana caranya</p>
            </div>
        </div>
    </div>

    <div class="p-5 md:p-6 space-y-6">

        {{-- TARGET ROLE --}}
        <div>
            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="fas fa-user-tag text-purple-500 mr-1"></i> Target Role
                </label>
                <div class="flex gap-2">
                    <button type="button" @click="toggleAll('target_roles', roles)"
                            class="text-xs text-cyan-600 hover:text-cyan-700 font-medium">
                        <i class="fas fa-check-double"></i> Pilih Semua
                    </button>
                    <span class="text-gray-300">|</span>
                    <button type="button" @click="clearAll('target_roles')"
                            class="text-xs text-red-500 hover:text-red-600 font-medium">
                        Bersihkan
                    </button>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="r in roles" :key="r.id">
                    <label class="chip-check" :class="{ 'selected': form.target_roles.includes(r.id) }">
                        <input type="checkbox" :value="r.id" x-model="form.target_roles">
                        <i class="fas text-xs" :class="r.icon"></i>
                        <span x-text="r.label"></span>
                    </label>
                </template>
            </div>
        </div>

        {{-- TARGET GROUP/DIVISI/GENERASI --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                <i class="fas fa-layer-group text-purple-500 mr-1"></i> Target Berdasarkan
            </label>

            <div class="flex flex-wrap gap-2 mb-4 p-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl">
                <template x-for="t in [
                    {id:'all',        label:'Semua',      icon:'fa-users'},
                    {id:'group',      label:'Kelompok',   icon:'fa-layer-group'},
                    {id:'division',   label:'Divisi',     icon:'fa-sitemap'},
                    {id:'generation', label:'Generasi',   icon:'fa-stream'},
                    {id:'mixed',      label:'Campuran',   icon:'fa-random'}
                ]" :key="t.id">
                    <button type="button" @click="form.target_type = t.id"
                            :class="form.target_type === t.id
                                ? 'bg-white dark:bg-gray-800 text-cyan-600 shadow-sm'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-white/50'"
                            class="flex-1 min-w-[80px] px-3 py-2 rounded-lg text-xs md:text-sm font-medium transition-all flex items-center justify-center gap-2">
                        <i class="fas" :class="t.icon"></i>
                        <span x-text="t.label"></span>
                    </button>
                </template>
            </div>

            {{-- Panel Kelompok --}}
            <div x-show="['group','mixed'].includes(form.target_type)" x-transition class="mb-4">
                <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kelompok</p>
                    <div class="flex gap-2">
                        <button type="button" @click="toggleAll('target_groups', groups)" class="text-xs text-cyan-600 hover:underline">Pilih Semua</button>
                        <button type="button" @click="clearAll('target_groups')" class="text-xs text-red-500 hover:underline">Bersihkan</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-56 overflow-y-auto p-2 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    <template x-for="g in groups" :key="g.id">
                        <label class="chip-check !py-1.5 !px-2.5" :class="{ 'selected': form.target_groups.includes(g.id) }">
                            <input type="checkbox" :value="g.id" x-model="form.target_groups">
                            <span class="text-xs truncate" x-text="g.name"></span>
                        </label>
                    </template>
                    <template x-if="groups.length === 0">
                        <p class="col-span-full text-center text-xs text-gray-400 py-3">Belum ada kelompok</p>
                    </template>
                </div>
            </div>

            {{-- Panel Divisi --}}
            <div x-show="['division','mixed'].includes(form.target_type)" x-transition class="mb-4">
                <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Divisi</p>
                    <div class="flex gap-2">
                        <button type="button" @click="toggleAll('target_divisions', divisions)" class="text-xs text-cyan-600 hover:underline">Pilih Semua</button>
                        <button type="button" @click="clearAll('target_divisions')" class="text-xs text-red-500 hover:underline">Bersihkan</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-56 overflow-y-auto p-2 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    <template x-for="d in divisions" :key="d.id">
                        <label class="chip-check !py-1.5 !px-2.5" :class="{ 'selected': form.target_divisions.includes(d.id) }">
                            <input type="checkbox" :value="d.id" x-model="form.target_divisions">
                            <span class="text-xs truncate" x-text="d.name"></span>
                        </label>
                    </template>
                    <template x-if="divisions.length === 0">
                        <p class="col-span-full text-center text-xs text-gray-400 py-3">Belum ada divisi</p>
                    </template>
                </div>
            </div>

            {{-- Panel Generasi --}}
            <div x-show="['generation','mixed'].includes(form.target_type)" x-transition class="mb-4">
                <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Generasi</p>
                    <div class="flex gap-2">
                        <button type="button" @click="toggleAll('target_generations', generations)" class="text-xs text-cyan-600 hover:underline">Pilih Semua</button>
                        <button type="button" @click="clearAll('target_generations')" class="text-xs text-red-500 hover:underline">Bersihkan</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-56 overflow-y-auto p-2 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    <template x-for="g in generations" :key="g.id">
                        <label class="chip-check !py-1.5 !px-2.5" :class="{ 'selected': form.target_generations.includes(g.id) }">
                            <input type="checkbox" :value="g.id" x-model="form.target_generations">
                            <span class="text-xs truncate" x-text="g.name"></span>
                        </label>
                    </template>
                    <template x-if="generations.length === 0">
                        <p class="col-span-full text-center text-xs text-gray-400 py-3">Belum ada generasi</p>
                    </template>
                </div>
            </div>
        </div>

        {{-- METODE ABSENSI --}}
        <div>
            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="fas fa-fingerprint text-purple-500 mr-1"></i> Metode Absensi <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <button type="button" @click="form.allowed_methods = methods.map(m => m.id)" class="text-xs text-cyan-600 hover:underline">Semua</button>
                    <button type="button" @click="form.allowed_methods = []" class="text-xs text-red-500 hover:underline">Bersihkan</button>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <template x-for="m in methods" :key="m.id">
                    <label class="choice-card" :class="{ 'selected': form.allowed_methods.includes(m.id) }">
                        <input type="checkbox" class="sr-only" :value="m.id"
                               @change="toggleMethod(m.id)" :checked="form.allowed_methods.includes(m.id)">
                        <i class="fas choice-card-icon" :class="m.icon"></i>
                        <span class="font-semibold text-gray-700 dark:text-gray-300 text-sm" x-text="m.label"></span>
                        <span class="text-[10px] text-gray-400" x-text="m.desc"></span>
                    </label>
                </template>
            </div>
            <p x-show="form.allowed_methods.length === 0" class="text-xs text-red-500 mt-2">
                <i class="fas fa-exclamation-triangle"></i> Pilih minimal satu metode.
            </p>
        </div>
    </div>
</div>