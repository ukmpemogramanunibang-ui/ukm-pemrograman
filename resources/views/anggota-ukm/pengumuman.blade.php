{{-- resources/views/anggota/pengumuman.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Pengumuman & Notifikasi — Anggota')
@section('breadcrumb', 'Pengumuman & Notifikasi')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Pengumuman & Notifikasi',
        'icon'        => 'fa-bullhorn',
        'tone'        => 'amber',
        'description' => 'Pusat informasi, pengumuman resmi, dan notifikasi pribadi Anda.',
        'progress'    => 60,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-newspaper', 'text' => 'Pengumuman resmi dari pengurus'],
        ['icon' => 'fa-filter', 'text' => 'Filter kategori & prioritas'],
        ['icon' => 'fa-envelope-open', 'text' => 'Tandai sudah dibaca'],
        ['icon' => 'fa-bell', 'text' => 'Push notification ke perangkat'],
        ],
    ])
@endsection