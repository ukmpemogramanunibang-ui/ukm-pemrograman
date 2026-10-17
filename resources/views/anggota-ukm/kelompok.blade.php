{{-- resources/views/anggota/kelompok.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Kelompok Saya — Anggota')
@section('breadcrumb', 'Kelompok Saya')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Kelompok Saya',
        'icon'        => 'fa-users-rectangle',
        'tone'        => 'emerald',
        'description' => 'Kelola profil kelompok, anggota, dan aktivitas kelompok Anda.',
        'progress'    => 40,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-circle-info', 'text' => 'Profil, visi, dan misi kelompok'],
        ['icon' => 'fa-user-group', 'text' => 'Daftar & struktur anggota'],
        ['icon' => 'fa-chart-line', 'text' => 'Statistik aktivitas kelompok'],
        ['icon' => 'fa-comments', 'text' => 'Diskusi internal kelompok'],
        ],
    ])
@endsection