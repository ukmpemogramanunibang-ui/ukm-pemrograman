/* ═══════════════════════════════════════════════════════════════
 * Attendance Wizard — Alpine.js Component
 * Versi: 1.0 · 2026
 * ═══════════════════════════════════════════════════════════════ */
function attendanceWizard() {
    const DATA = window.__ATTENDANCE_WIZARD_DATA__ || {};
    const old  = DATA.old || {};

    return {
        /* ── STATE ───────────────────────────── */
        step: 1,
        totalSteps: 3,
        showPreviewMobile: false,
        showConfirm: false,
        isSubmitting: false,

        /* ── FORM STATE ──────────────────────── */
        form: {
            title:               old.title || '',
            description:         old.description || '',
            attendance_type:     old.attendance_type || 'regular',
            attendance_mode:     old.attendance_mode || 'offline',
            schedule_id:         old.schedule_id || '',
            room_id:             old.room_id || '',
            shift_name:          old.shift_name || '',
            target_type:         old.target_type || 'all',
            target_roles:        Array.isArray(old.target_roles)       ? [...old.target_roles]       : [],
            target_groups:       Array.isArray(old.target_groups)      ? [...old.target_groups]      : [],
            target_divisions:    Array.isArray(old.target_divisions)   ? [...old.target_divisions]   : [],
            target_generations:  Array.isArray(old.target_generations) ? [...old.target_generations] : [],
            allowed_methods:     Array.isArray(old.allowed_methods) && old.allowed_methods.length
                                    ? [...old.allowed_methods] : ['manual'],
            scheduled_start_at:  old.scheduled_start_at || '',
            scheduled_end_at:    old.scheduled_end_at || '',
            attendance_open_at:  old.attendance_open_at || '',
            attendance_close_at: old.attendance_close_at || '',
        },

        /* ── MASTER DATA ─────────────────────── */
        schedules:   DATA.schedules   || [],
        rooms:       DATA.rooms       || [],
        groups:      DATA.groups      || [],
        divisions:   DATA.divisions   || [],
        generations: DATA.generations || [],

        /* ── STATIC CONFIGS ──────────────────── */
        activityTypes: [
            { id: 'regular',     label: 'Rutin',      icon: 'fa-sync-alt' },
            { id: 'event',       label: 'Event',      icon: 'fa-calendar-star' },
            { id: 'competition', label: 'Kompetisi',  icon: 'fa-trophy' },
            { id: 'meeting',     label: 'Rapat',      icon: 'fa-users' },
            { id: 'training',    label: 'Pelatihan',  icon: 'fa-dumbbell' },
            { id: 'workshop',    label: 'Workshop',   icon: 'fa-tools' },
            { id: 'seminar',     label: 'Seminar',    icon: 'fa-microphone' },
            { id: 'tournament',  label: 'Turnamen',   icon: 'fa-flag-checkered' },
            { id: 'external',    label: 'External',   icon: 'fa-external-link-alt' },
            { id: 'custom',      label: 'Lainnya',    icon: 'fa-ellipsis-h' },
        ],

        attendanceModes: [
            { id: 'offline', label: 'Tatap Muka', icon: 'fa-building',      desc: 'Hadir langsung di ruangan' },
            { id: 'online',  label: 'Online',     icon: 'fa-video',         desc: 'Melalui platform daring' },
            { id: 'hybrid',  label: 'Hybrid',     icon: 'fa-people-arrows', desc: 'Gabungan keduanya' },
        ],

        roles: [
            { id: 'owner',        label: 'Owner',        icon: 'fa-crown' },
            { id: 'superadmin',   label: 'Superadmin',   icon: 'fa-shield-alt' },
            { id: 'ketua',        label: 'Ketua',        icon: 'fa-user-tie' },
            { id: 'pengurus',     label: 'Pengurus',     icon: 'fa-users-cog' },
            { id: 'anggota_ukm',  label: 'Anggota UKM',  icon: 'fa-user-graduate' },
            { id: 'anggota_umum', label: 'Anggota Umum', icon: 'fa-user' },
            { id: 'pengunjung',   label: 'Pengunjung',   icon: 'fa-globe' },
        ],

        methods: [
            { id: 'manual', label: 'Manual',  icon: 'fa-hand-paper', desc: 'Input oleh petugas' },
            { id: 'nfc',    label: 'NFC',     icon: 'fa-wifi',       desc: 'Scan kartu NFC' },
            { id: 'qr',     label: 'QR Code', icon: 'fa-qrcode',     desc: 'Scan QR Code' },
        ],

        /* ── GETTERS ─────────────────────────── */
        get stepTitle() {
            return ['Informasi Dasar', 'Target & Metode', 'Waktu & Keamanan'][this.step - 1] || '';
        },

        get selectedSchedule() {
            return this.schedules.find(s => String(s.id) === String(this.form.schedule_id)) || null;
        },

        get selectedRoom() {
            return this.rooms.find(r => String(r.id) === String(this.form.room_id)) || null;
        },

        get targetSummary() {
            return {
                groups:      this.form.target_groups.length,
                divisions:   this.form.target_divisions.length,
                generations: this.form.target_generations.length,
                roles:       this.form.target_roles.length,
            };
        },

        /* ── STEP LABELS ─────────────────────── */
        stepLabel(n) {
            return ['Informasi Dasar', 'Target & Metode', 'Waktu & Keamanan'][n - 1] || '';
        },

        /* ── VALIDASI PER-STEP ───────────────── */
        isStepValid(step) {
            if (step === 1) {
                return this.form.title.trim() !== ''
                    && this.form.attendance_type
                    && this.form.attendance_mode;
            }
            if (step === 2) {
                return this.form.allowed_methods.length > 0;
            }
            if (step === 3) {
                return this.form.scheduled_start_at
                    && this.form.scheduled_end_at
                    && this.form.attendance_open_at
                    && this.form.attendance_close_at;
            }
            return true;
        },

        /* ── NAVIGASI ────────────────────────── */
        nextStep() {
            if (!this.isStepValid(this.step)) {
                const missing = this.getMissingFields(this.step);
                this.notify('error', `Lengkapi dulu: ${missing.join(', ')}`);
                return;
            }
            if (this.step < this.totalSteps) {
                this.step++;
                this.scrollTop();
            }
        },

        prevStep() {
            if (this.step > 1) {
                this.step--;
                this.scrollTop();
            }
        },

        goToStep(n) {
            if (n < this.step) {
                this.step = n;
                this.scrollTop();
                return;
            }
            if (this.isStepValid(this.step)) {
                this.step = n;
                this.scrollTop();
            } else {
                this.notify('error', 'Lengkapi kolom wajib dulu.');
            }
        },

        scrollTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        getMissingFields(step) {
            const missing = [];
            if (step === 1) {
                if (!this.form.title.trim())      missing.push('Judul');
                if (!this.form.attendance_type)   missing.push('Tipe Kegiatan');
                if (!this.form.attendance_mode)   missing.push('Mode');
            }
            if (step === 2) {
                if (this.form.allowed_methods.length === 0) missing.push('Metode Absensi');
            }
            if (step === 3) {
                if (!this.form.scheduled_start_at)  missing.push('Mulai');
                if (!this.form.scheduled_end_at)    missing.push('Selesai');
                if (!this.form.attendance_open_at)  missing.push('Absen Dibuka');
                if (!this.form.attendance_close_at) missing.push('Absen Ditutup');
            }
            return missing;
        },

        /* ── SCHEDULE SYNC ───────────────────── */
        onScheduleChange() {
            const s = this.selectedSchedule;
            if (!s) return;

            const fmt = (str) => {
                if (!str) return '';
                const d = new Date(str);
                if (isNaN(d)) return str;
                const pad = n => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            };

            if (s.start_datetime) {
                this.form.scheduled_start_at = fmt(s.start_datetime);
                this.form.attendance_open_at = fmt(s.start_datetime);
            }
            if (s.end_datetime) {
                this.form.scheduled_end_at    = fmt(s.end_datetime);
                this.form.attendance_close_at = fmt(s.end_datetime);
            }
            if (s.room_id && !this.form.room_id) {
                this.form.room_id = s.room_id;
            }

            this.notify('success', 'Waktu disinkronkan dari jadwal.');
        },

        /* ── MULTI-SELECT HELPERS ────────────── */
        isAllSelected(key, list) {
            return list.length > 0 && this.form[key].length === list.length;
        },

        toggleAll(key, list) {
            if (this.isAllSelected(key, list)) {
                this.form[key] = [];
            } else {
                this.form[key] = list.map(i => i.id);
            }
        },

        clearAll(key) {
            this.form[key] = [];
        },

        toggleMethod(id) {
            const i = this.form.allowed_methods.indexOf(id);
            if (i >= 0) this.form.allowed_methods.splice(i, 1);
            else        this.form.allowed_methods.push(id);
        },

        /* ── FORMAT DATE ─────────────────────── */
        formatDate(dt) {
            if (!dt) return '—';
            const d = new Date(dt);
            if (isNaN(d)) return dt;
            try {
                return d.toLocaleString('id-ID', {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            } catch {
                return dt;
            }
        },

        /* ── PREVIEW HTML ────────────────────── */
        previewHtml() {
            const f = this.form;
            const s = this.selectedSchedule;
            const r = this.selectedRoom;

            const methodBadges = f.allowed_methods.length
                ? f.allowed_methods.map(m =>
                    `<span class="inline-block px-2 py-0.5 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 text-[10px] font-bold uppercase mr-1">${m}</span>`
                  ).join('')
                : '<span class="text-red-400 text-xs">Belum dipilih</span>';

            const roleBadges = f.target_roles.length
                ? f.target_roles.map(role =>
                    `<span class="inline-block px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-[10px] font-bold capitalize mr-1">${role.replace('_',' ')}</span>`
                  ).join('')
                : '<span class="text-gray-400 text-xs italic">Semua role</span>';

            return `
                <div class="space-y-4 text-sm">
                    <div class="pb-3 border-b border-dashed border-gray-200 dark:border-gray-700">
                        <h4 class="font-bold text-gray-800 dark:text-white text-base mb-1">${f.title || '<span class="text-gray-400">Judul belum diisi</span>'}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${f.description || 'Tanpa deskripsi'}</p>
                    </div>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between"><dt class="text-gray-500">Tipe</dt><dd class="font-medium capitalize">${f.attendance_type}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Mode</dt><dd class="font-medium capitalize">${f.attendance_mode}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Jadwal</dt><dd class="font-medium truncate max-w-[150px]">${s ? s.title : '—'}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Ruangan</dt><dd class="font-medium truncate max-w-[150px]">${r ? r.name : '—'}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Mulai</dt><dd class="font-medium">${this.formatDate(f.scheduled_start_at)}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Selesai</dt><dd class="font-medium">${this.formatDate(f.scheduled_end_at)}</dd></div>
                    </dl>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Target Role</p>
                        <div>${roleBadges}</div>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Metode</p>
                        <div>${methodBadges}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center"><p class="text-lg font-bold text-purple-600">${f.target_groups.length}</p><p class="text-[10px] text-gray-500">Kelompok</p></div>
                        <div class="text-center"><p class="text-lg font-bold text-pink-600">${f.target_divisions.length}</p><p class="text-[10px] text-gray-500">Divisi</p></div>
                        <div class="text-center"><p class="text-lg font-bold text-indigo-600">${f.target_generations.length}</p><p class="text-[10px] text-gray-500">Generasi</p></div>
                    </div>
                </div>
            `;
        },

        /* ── OPEN CONFIRM ────────────────────── */
        openPreview() {
            for (let s = 1; s <= this.totalSteps; s++) {
                if (!this.isStepValid(s)) {
                    this.step = s;
                    const missing = this.getMissingFields(s);
                    this.notify('error', `Tahap ${s}: lengkapi ${missing.join(', ')}`);
                    this.scrollTop();
                    return;
                }
            }
            this.showConfirm = true;
        },

        /* ── SUBMIT ──────────────────────────── */
        submitForm() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            // Validasi waktu logis
            if (this.form.scheduled_start_at >= this.form.scheduled_end_at) {
                this.isSubmitting = false;
                this.notify('error', 'Waktu mulai harus sebelum waktu selesai.');
                return;
            }
            if (this.form.attendance_open_at >= this.form.attendance_close_at) {
                this.isSubmitting = false;
                this.notify('error', 'Waktu buka absensi harus sebelum tutup.');
                return;
            }

            this.$refs.form.submit();
        },

        /* ── TOAST HELPER ────────────────────── */
        notify(type, message) {
            this.$dispatch('notify', { type, message });
        },
    };
}

window.attendanceWizard = attendanceWizard;