<?php

return [
    'page_size'     => 20,
    'dropdown_size' => 8,

    'sources' => [
       'default'            => ['emoji' => '🔔', 'label' => 'Umum'],
        'group_announcement' => ['emoji' => '📢', 'label' => 'Pengumuman'],
        'booking'            => ['emoji' => '📅', 'label' => 'Pemesanan'],
        'attendance'         => ['emoji' => '✋', 'label' => 'Presensi'],
        'lms'                => ['emoji' => '📘', 'label' => 'LMS'],
        'chat'               => ['emoji' => '💬', 'label' => 'Pesan'],
        'financial'          => ['emoji' => '💰', 'label' => 'Keuangan'],
        'system'             => ['emoji' => '⚙️', 'label' => 'Sistem'],
        ],

    'priority' => [
        'low'    => ['label' => 'Rendah',  'color' => 'slate'],
        'normal' => ['label' => 'Normal',  'color' => 'cyan'],
        'high'   => ['label' => 'Tinggi',  'color' => 'amber'],
        'urgent' => ['label' => 'Mendesak','color' => 'rose'],
    ],
];