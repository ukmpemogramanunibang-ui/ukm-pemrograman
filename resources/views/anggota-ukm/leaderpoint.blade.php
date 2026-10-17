{{-- resources/views/anggota/leaderpoint.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Leaderpoint — Anggota')
@section('breadcrumb', 'Leaderpoint')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Leaderpoint',
        'icon'        => 'fa-ranking-star',
        'tone'        => 'yellow',
        'description' => 'Peringkat dan capaian Anda di komunitas UNICODE.',
        'progress'    => 40,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-trophy', 'text' => 'Leaderboard global'],
        ['icon' => 'fa-users-rectangle', 'text' => 'Peringkat per kelompok'],
        ['icon' => 'fa-sitemap', 'text' => 'Peringkat per divisi'],
        ['icon' => 'fa-scroll', 'text' => 'Riwayat & detail poin'],
        ],
    ])
@endsection