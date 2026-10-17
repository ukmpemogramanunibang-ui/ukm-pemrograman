<?php
// config/impersonation.php

return [
    /* ═══════════════════════════════════════════════════════
     | TOKEN
     * ═══════════════════════════════════════════════════════ */
    'token' => [
        'length'        => 48,
        'ttl_minutes'   => 480,   // 8 jam kerja
        'max_per_owner' => 7,
        'auto_cleanup'  => true,
    ],

    /* ═══════════════════════════════════════════════════════
     | SECURITY
     * ═══════════════════════════════════════════════════════ */
    'security' => [
        'bind_ip'              => true,
        'bind_user_agent'      => true,
        'ua_tolerance_chars'   => 40,
        'rotate_after_minutes' => 60,
        'revalidate_every'     => 300,   // detik — cache validation
    ],

    /* ═══════════════════════════════════════════════════════
     | RATE LIMIT
     * ═══════════════════════════════════════════════════════ */
    'rate_limit' => [
        'attempts'        => 5,
        'decay_seconds'   => 60,
        'lockout_seconds' => 300,
    ],

    /* ═══════════════════════════════════════════════════════
     | CONCURRENT SESSION (Fitur Utama)
     * ═══════════════════════════════════════════════════════ */
    'concurrent' => [
        'enabled'                   => true,
        'detect_real_user_presence' => true,
        'presence_window_seconds'   => 120,     // user asli dianggap "aktif" jika last_seen < 2 menit
        'silent_mode'               => true,    // aktivitas Owner tidak mengotori log user asli
        'isolate_session'           => true,    // session key namespace terpisah
    ],

    /* ═══════════════════════════════════════════════════════
     | ROLE → SLUG (level_id → slug)
     * ═══════════════════════════════════════════════════════ */
    'roles' => [
        1 => 'owner',
        2 => 'superadmin',
        3 => 'ketua',
        4 => 'pengurus',
        5 => 'anggota-ukm',    // ← level 5 = anggota (default anggota-ukm)
        6 => 'pengunjung',
    ],

    /* Khusus level 5 — bedakan anggota-ukm vs anggota-umum */
    'anggota_umum_marker' => [
        'name'         => 'Anggota Umum',
        'account_type' => 'umum',
    ],

    /* ═══════════════════════════════════════════════════════
     | ROLE → VIEW
     * ═══════════════════════════════════════════════════════ */
    'views' => [
        'owner'        => 'owner.dashboard',
        'superadmin'   => 'superadmin.dashboard',
        'ketua'        => 'dashboard.ketua',
        'pengurus'     => 'pengurus.dashboard',
        'anggota-ukm'  => 'anggota-ukm.dashboard',
        'anggota-umum' => 'anggota-umum.dashboard',
        'pengunjung'   => 'pengunjung.beranda',
    ],

    /* ═══════════════════════════════════════════════════════
     | SESSION KEYS (isolation namespace)
     * ═══════════════════════════════════════════════════════ */
    'session_keys' => [
        'active'         => 'owner.explore.active',
        'owner_id'       => 'owner.explore.owner_id',
        'user_id'        => 'owner.explore.user_id',
        'role_slug'      => 'owner.explore.role_slug',
        'token'          => 'owner.explore.token',
        'token_id'       => 'owner.explore.token_id',
        'started_at'     => 'owner.explore.started_at',
        'last_activity'  => 'owner.explore.last_activity_at',
        'last_validated' => 'owner.explore.last_validated_at',
        'radar_last'     => 'owner.explore.radar_last_seen',
    ],
];