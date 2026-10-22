<?php

namespace App\Http\Controllers;

class OrganisasiController extends Controller
{
    /* Metadata ringkas untuk halaman direktori (index) */
    public static function meta(): array
    {
        return [
            'bem' => [
                'slug' => 'bem', 'name' => 'BEM', 'full_name' => 'Badan Eksekutif Mahasiswa',
                'tagline' => 'Suara Mahasiswa, Aksi Nyata',
                'logo' => 'images/organisasi/bem.png', 'gradient' => 'from-cyan-500 to-electric',
                'stats' => ['Anggota' => 48, 'Proker' => 12, 'Prestasi' => 5],
            ],
            'hima-informatika' => [
                'slug' => 'hima-informatika', 'name' => 'HIMA Informatika',
                'full_name' => 'Himpunan Mahasiswa Informatika',
                'tagline' => 'Koding Hari Ini, Inovasi Esok Hari',
                'logo' => 'images/organisasi/hima-informatika.png', 'gradient' => 'from-blue-500 to-cyan-400',
                'stats' => ['Anggota' => 120, 'Proker' => 18, 'Prestasi' => 12],
            ],
            'hima-kebidanan' => [
                'slug' => 'hima-kebidanan', 'name' => 'HIMA Kebidanan',
                'full_name' => 'Himpunan Mahasiswa Kebidanan',
                'tagline' => 'Sehat Ibu, Sehat Generasi',
                'logo' => 'images/organisasi/hima-kebidanan.png', 'gradient' => 'from-pink-500 to-rose-400',
                'stats' => ['Anggota' => 85, 'Proker' => 14, 'Prestasi' => 8],
            ],
            'hima-keperawatan' => [
                'slug' => 'hima-keperawatan', 'name' => 'HIMA Keperawatan',
                'full_name' => 'Himpunan Mahasiswa Keperawatan',
                'tagline' => 'Merawat dengan Hati, Melayani dengan Ilmu',
                'logo' => 'images/organisasi/hima-keperawatan.png', 'gradient' => 'from-emerald-500 to-teal-400',
                'stats' => ['Anggota' => 95, 'Proker' => 15, 'Prestasi' => 7],
            ],
            'hima-manajemen' => [
                'slug' => 'hima-manajemen', 'name' => 'HIMA Manajemen',
                'full_name' => 'Himpunan Mahasiswa Manajemen',
                'tagline' => 'Strategic Mind, Business Heart',
                'logo' => 'images/organisasi/hima-manajemen.png', 'gradient' => 'from-amber-500 to-orange-400',
                'stats' => ['Anggota' => 110, 'Proker' => 16, 'Prestasi' => 9],
            ],
            'hima-hukum' => [
                'slug' => 'hima-hukum', 'name' => 'HIMA Hukum',
                'full_name' => 'Himpunan Mahasiswa Hukum',
                'tagline' => 'Iustitia et Veritas',
                'logo' => 'images/organisasi/hima-hukum.png', 'gradient' => 'from-indigo-500 to-violet-400',
                'stats' => ['Anggota' => 75, 'Proker' => 11, 'Prestasi' => 6],
            ],
            'hima-ars' => [
                'slug' => 'hima-ars', 'name' => 'HIMA ARS',
                'full_name' => 'Himpunan Mahasiswa Administrasi Rumah Sakit',
                'tagline' => 'Manajemen Sehat, Layanan Bermutu',
                'logo' => 'images/organisasi/hima-ars.png', 'gradient' => 'from-sky-500 to-blue-400',
                'stats' => ['Anggota' => 60, 'Proker' => 10, 'Prestasi' => 4],
            ],
        ];
    }

    public function index()
    {
        return view('organisasi.index', ['items' => self::meta()]);
    }

    /* ── Setiap method return view-nya sendiri ── */
    public function bem()             { return view('organisasi.bem'); }
    public function himaInformatika() { return view('organisasi.hima-informatika'); }
    public function himaKebidanan()   { return view('organisasi.hima-kebidanan'); }
    public function himaKeperawatan() { return view('organisasi.hima-keperawatan'); }
    public function himaManajemen()   { return view('organisasi.hima-manajemen'); }
    public function himaHukum()       { return view('organisasi.hima-hukum'); }
    public function himaArs()         { return view('organisasi.hima-ars'); }
}