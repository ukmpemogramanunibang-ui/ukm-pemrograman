<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengurusController extends Controller
{
    // Halaman Dashboard Utama Pengurus
    public function index()
    {
        return view('dashboard.pengurus');
    }

    // Halaman Tugas
    public function tugas()
    {
        // Buat file dashboard/pengurus/tugas.blade.php nanti
        return view('dashboard.pengurus.tugas'); 
    }

    // Halaman Kelompok
    public function kelompok()
    {
        return view('dashboard.pengurus.kelompok');
    }

    // Halaman Pengumuman
    public function pengumuman()
    {
        return view('dashboard.pengurus.pengumuman');
    }

    // Halaman Kontak
    public function kontak()
    {
        return view('dashboard.pengurus.kontak');
    }

    // Halaman Anggota
    public function anggota()
    {
        return view('dashboard.pengurus.anggota');
    }

    // Halaman Data Diri
    public function dataDiri()
    {
        return view('dashboard.pengurus.data_diri');
    }

    // Halaman Pengaturan
    public function pengaturan()
    {
        return view('dashboard.pengurus.pengaturan');
    }

    // Halaman Peringatan
    public function peringatan()
    {
        return view('dashboard.pengurus.peringatan');
    }
}