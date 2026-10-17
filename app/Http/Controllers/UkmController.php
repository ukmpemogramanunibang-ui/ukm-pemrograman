<?php

namespace App\Http\Controllers;

class UkmController extends Controller
{
    public static function meta(): array
    {
        return [
            'pemrograman' => [
                'slug' => 'pemrograman', 'name' => 'UKM Pemrograman',
                'full_name' => 'Unit Kegiatan Mahasiswa Pemrograman UNICODE',
                'tagline' => 'Code. Create. Innovate.',
                'logo' => 'images/logo_ukm3.jpeg', 'gradient' => 'from-cyan-500 to-electric',
                'stats' => ['Anggota' => 78, 'Proker' => 20, 'Prestasi' => 14],
            ],
            'multimedia' => [
                'slug' => 'multimedia', 'name' => 'UKM Multimedia',
                'full_name' => 'Unit Kegiatan Mahasiswa Multimedia',
                'tagline' => 'Visualize Your Vision',
                'logo' => 'images/ukm/multimedia.png', 'gradient' => 'from-fuchsia-500 to-pink-400',
                'stats' => ['Anggota' => 65, 'Proker' => 14, 'Prestasi' => 8],
            ],
            'nova' => [
                'slug' => 'nova', 'name' => 'UKM Nova',
                'full_name' => 'Unit Kegiatan Mahasiswa Nova — Kewirausahaan',
                'tagline' => 'Think Big, Start Small',
                'logo' => 'images/ukm/nova.png', 'gradient' => 'from-amber-500 to-yellow-400',
                'stats' => ['Anggota' => 55, 'Proker' => 12, 'Prestasi' => 6],
            ],
            'badminton' => [
                'slug' => 'badminton', 'name' => 'UKM Badminton',
                'full_name' => 'Unit Kegiatan Mahasiswa Badminton',
                'tagline' => 'Smash Your Limits',
                'logo' => 'images/ukm/badminton.png', 'gradient' => 'from-green-500 to-emerald-400',
                'stats' => ['Anggota' => 42, 'Proker' => 8, 'Prestasi' => 11],
            ],
            'basket' => [
                'slug' => 'basket', 'name' => 'UKM Basket',
                'full_name' => 'Unit Kegiatan Mahasiswa Basket',
                'tagline' => 'One Team, One Dream',
                'logo' => 'images/ukm/basket.png', 'gradient' => 'from-orange-500 to-red-400',
                'stats' => ['Anggota' => 38, 'Proker' => 9, 'Prestasi' => 10],
            ],
            'teater-teras' => [
                'slug' => 'teater-teras', 'name' => 'UKM Teater Teras',
                'full_name' => 'Unit Kegiatan Mahasiswa Teater Teras',
                'tagline' => 'Seni yang Menyentuh Jiwa',
                'logo' => 'images/ukm/teater-teras.png', 'gradient' => 'from-purple-500 to-indigo-400',
                'stats' => ['Anggota' => 35, 'Proker' => 10, 'Prestasi' => 7],
            ],
            'aksara-tarana' => [
                'slug' => 'aksara-tarana', 'name' => 'UKM Aksara Tarana',
                'full_name' => 'Unit Kegiatan Mahasiswa Aksara Tarana',
                'tagline' => 'Kata yang Menggerakkan',
                'logo' => 'images/ukm/aksara-tarana.png', 'gradient' => 'from-teal-500 to-cyan-400',
                'stats' => ['Anggota' => 30, 'Proker' => 8, 'Prestasi' => 5],
            ],
            'savora-padus' => [
                'slug' => 'savora-padus', 'name' => 'UM Savora Padus',
                'full_name' => 'Unit Musik Savora Paduan Suara',
                'tagline' => 'Satu Suara, Satu Hati',
                'logo' => 'images/ukm/savora-padus.png', 'gradient' => 'from-rose-500 to-pink-400',
                'stats' => ['Anggota' => 40, 'Proker' => 9, 'Prestasi' => 8],
            ],
            'futsal' => [
                'slug' => 'futsal', 'name' => 'UKM Futsal',
                'full_name' => 'Unit Kegiatan Mahasiswa Futsal',
                'tagline' => 'Play Hard, Win Harder',
                'logo' => 'images/ukm/futsal.png', 'gradient' => 'from-lime-500 to-green-400',
                'stats' => ['Anggota' => 45, 'Proker' => 8, 'Prestasi' => 12],
            ],
        ];
    }

    public function index()
    {
        return view('ukm.index', ['items' => self::meta()]);
    }

    public function pemrograman()  { return view('ukm.pemrograman'); }
    public function multimedia()   { return view('ukm.multimedia'); }
    public function nova()         { return view('ukm.nova'); }
    public function badminton()    { return view('ukm.badminton'); }
    public function basket()       { return view('ukm.basket'); }
    public function teaterTeras()  { return view('ukm.teater-teras'); }
    public function aksaraTarana() { return view('ukm.aksara-tarana'); }
    public function savoraPadus()  { return view('ukm.savora-padus'); }
    public function futsal()       { return view('ukm.futsal'); }
}