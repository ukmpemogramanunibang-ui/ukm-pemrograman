<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Single source of truth untuk menu navigasi Ketua.
 *
 * Header (search command palette) & sidebar membaca dari class ini,
 * sehingga tidak mungkin desync. Untuk menambah menu baru, cukup
 * tambah 1 entry di {@see items()} — kedua komponen langsung ikut.
 *
 * Struktur item:
 *  - ['type' => 'link',  'route' => 'ketua.*', 'active' => [...], ...]
 *  - ['type' => 'group', 'children' => [...], 'active' => [...], ...]
 */
final class KetuaMenu
{
    /**
     * Menu tree lengkap, urut sesuai tampilan sidebar.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function items(): array
    {
        return [
            // ═══════ Dashboard ═══════
            [
                'type'     => 'link',
                'key'      => 'dashboard',
                'label'    => 'Dashboard',
                'route'    => 'ketua.dashboard',
                'icon'     => 'fa-home',
                'section'  => 'Utama',
                'keywords' => 'dashboard home beranda utama depan start',
                'active'   => ['ketua.dashboard'],
                'emphasis' => true,
            ],

            // ═══════ Manajemen (group) ═══════
            [
                'type'     => 'group',
                'key'      => 'manajemen',
                'label'    => 'Manajemen',
                'active'   => [
                    'ketua.manajemen-anggota.*',
                    'ketua.manajemen-pengurus.*',
                    'ketua.jabatan.*',
                    'ketua.divisi.*',
                    'ketua.kelompok.*',
                ],
                'children' => [
                    [
                        'type'     => 'link',
                        'key'      => 'anggota',
                        'label'    => 'Anggota',
                        'route'    => 'ketua.manajemen-anggota.index',
                        'icon'     => 'fa-users',
                        'section'  => 'Manajemen',
                        'keywords' => 'anggota member user manajemen data',
                        'active'   => ['ketua.manajemen-anggota.*'],
                    ],
                    [
                        'type'     => 'link',
                        'key'      => 'pengurus',
                        'label'    => 'Pengurus',
                        'route'    => 'ketua.manajemen-pengurus.index',
                        'icon'     => 'fa-user-tie',
                        'section'  => 'Manajemen',
                        'keywords' => 'pengurus board manajemen staff',
                        'active'   => ['ketua.manajemen-pengurus.*'],
                    ],
                    [
                        'type'     => 'link',
                        'key'      => 'jabatan',
                        'label'    => 'Jabatan',
                        'route'    => 'ketua.jabatan.index',
                        'icon'     => 'fa-briefcase',
                        'section'  => 'Manajemen',
                        'keywords' => 'jabatan posisi role title',
                        'active'   => ['ketua.jabatan.*'],
                    ],
                    [
                        'type'     => 'link',
                        'key'      => 'divisi',
                        'label'    => 'Divisi',
                        'route'    => 'ketua.divisi.index',
                        'icon'     => 'fa-sitemap',
                        'section'  => 'Manajemen',
                        'keywords' => 'divisi department departemen',
                        'active'   => ['ketua.divisi.*'],
                    ],
                    [
                        'type'     => 'link',
                        'key'      => 'kelompok',
                        'label'    => 'Kelompok',
                        'route'    => 'ketua.kelompok.index',
                        'icon'     => 'fa-layer-group',
                        'section'  => 'Manajemen',
                        'keywords' => 'kelompok grup group',
                        'active'   => ['ketua.kelompok.*'],
                    ],
                ],
            ],

            // ═══════ Standalone: Operasional ═══════
            [
                'type'     => 'link',
                'key'      => 'kegiatan',
                'label'    => 'Kegiatan & Event',
                'route'    => 'ketua.kegiatan',
                'icon'     => 'fa-calendar-check',
                'section'  => 'Operasional',
                'keywords' => 'kegiatan event acara agenda activity',
                'active'   => ['ketua.kegiatan'],
            ],
            [
                'type'     => 'link',
                'key'      => 'presensi',
                'label'    => 'Presensi QR',
                'route'    => 'ketua.presensi',
                'icon'     => 'fa-qrcode',
                'section'  => 'Operasional',
                'keywords' => 'presensi kehadiran absen qr attendance',
                'active'   => ['ketua.presensi'],
            ],
            [
                'type'     => 'link',
                'key'      => 'keuangan',
                'label'    => 'Uangkas',
                'route'    => 'ketua.keuangan',
                'icon'     => 'fa-coins',
                'section'  => 'Operasional',
                'keywords' => 'uangkas kas keuangan iuran uang finance',
                'active'   => ['ketua.keuangan'],
            ],
            [
                'type'     => 'link',
                'key'      => 'leaderpoint',
                'label'    => 'LeaderPoint',
                'route'    => 'ketua.poin-prestasi',
                'icon'     => 'fa-medal',
                'section'  => 'Prestasi',
                'keywords' => 'leaderpoint poin prestasi skor medal leaderboard',
                'active'   => ['ketua.poin-prestasi'],
            ],

            // ═══════ Komunikasi (group) ═══════
            [
                'type'     => 'group',
                'key'      => 'komunikasi',
                'label'    => 'Komunikasi',
                'active'   => ['ketua.pengumuman.*'],
                'children' => [
                    [
                        'type'     => 'link',
                        'key'      => 'pengumuman',
                        'label'    => 'Pengumuman',
                        'route'    => 'ketua.pengumuman.index',
                        'icon'     => 'fa-bullhorn',
                        'section'  => 'Komunikasi',
                        'keywords' => 'pengumuman announcement komun komunikasi info',
                        'active'   => ['ketua.pengumuman.*'],
                    ],
                ],
            ],

            // ═══════ Standalone: Sistem & Pelaporan ═══════
            [
                'type'     => 'link',
                'key'      => 'approval',
                'label'    => 'Approval Center',
                'route'    => 'ketua.approval',
                'icon'     => 'fa-check-double',
                'section'  => 'Operasional',
                'keywords' => 'approval persetujuan approve center',
                'active'   => ['ketua.approval'],
            ],
            [
                'type'     => 'link',
                'key'      => 'laporan',
                'label'    => 'Laporan & Analitik',
                'route'    => 'ketua.laporan',
                'icon'     => 'fa-chart-line',
                'section'  => 'Operasional',
                'keywords' => 'laporan report analitik statistik data chart',
                'active'   => ['ketua.laporan'],
            ],
            [
                'type'     => 'link',
                'key'      => 'notifications',
                'label'    => 'Notification Center',
                'route'    => 'ketua.notifications.index',
                'icon'     => 'fa-bell',
                'section'  => 'Sistem',
                'keywords' => 'notification notifikasi notif pesan bell inbox',
                'active'   => ['ketua.notifications.*'],
                'badge'    => 'sidebar-notif-badge',
            ],
            [
                'type'     => 'link',
                'key'      => 'pengaturan',
                'label'    => 'Pengaturan',
                'route'    => 'ketua.pengaturan',
                'icon'     => 'fa-cog',
                'section'  => 'Sistem',
                'keywords' => 'pengaturan setting config profile profil akun',
                'active'   => ['ketua.pengaturan'],
            ],
        ];
    }

    /**
     * Flat list siap pakai untuk command palette header.
     *
     * Item yang route-nya tidak terdaftar otomatis di-skip, sehingga
     * user tidak pernah melihat hasil pencarian yang dead link.
     *
     * @return array<int, array{label: string, url: string, icon: string, section: string, keywords: string}>
     */
    public static function searchable(): array
    {
        $result = [];

        foreach (self::items() as $item) {
            if ($item['type'] === 'link') {
                if ($entry = self::toSearchItem($item)) {
                    $result[] = $entry;
                }
            } elseif ($item['type'] === 'group') {
                foreach ($item['children'] as $child) {
                    if ($entry = self::toSearchItem($child)) {
                        $result[] = $entry;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Resolve nama route ke URL, fallback ke '#' bila tidak ada.
     * Dipakai sidebar agar route yang hilang tidak memecah halaman.
     */
    public static function url(string $name, string $fallback = '#'): string
    {
        try {
            return Route::has($name) ? route($name) : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    /**
     * Cek apakah salah satu pola route cocok dengan route aktif saat ini.
     * Dipakai untuk highlight item aktif & auto-open group.
     *
     * @param array<int, string> $patterns
     */
    public static function isActive(array $patterns): bool
    {
        if (empty($patterns)) {
            return false;
        }

        try {
            return request()->routeIs(...$patterns);
        } catch (\Throwable $e) {
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────

    private static function toSearchItem(array $item): ?array
    {
        if (empty($item['route'])) {
            return null;
        }

        try {
            if (! Route::has($item['route'])) {
                return null;
            }

            return [
                'label'    => $item['label'],
                'url'      => route($item['route']),
                'icon'     => $item['icon'],
                'section'  => $item['section'] ?? 'Utama',
                'keywords' => $item['keywords'] ?? '',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }
}