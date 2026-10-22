{{-- resources/views/anggota/misi.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Misi — Anggota')
@section('breadcrumb', 'Misi')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Misi',
        'icon'        => 'fa-flag-checkered',
        'tone'        => 'orange',
        'description' => 'Selesaikan misi dan dapatkan poin untuk naik peringkat.',
        'progress'    => 30,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-list-check', 'text' => 'Misi harian & mingguan'],
        ['icon' => 'fa-star', 'text' => 'Misi spesial event'],
        ['icon' => 'fa-chart-simple', 'text' => 'Progress tracking'],
        ['icon' => 'fa-medal', 'text' => 'Reward, badge, dan leaderboard'],
        ],
    ])
@endsection