{{-- resources/views/anggota/game.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Game — Anggota')
@section('breadcrumb', 'Game')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Game',
        'icon'        => 'fa-gamepad',
        'tone'        => 'purple',
        'description' => 'Game interaktif, kuis, dan challenge seru dari UNICODE.',
        'progress'    => 25,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-brain', 'text' => 'Kuis & challenge interaktif'],
        ['icon' => 'fa-dice', 'text' => 'Mini games casual'],
        ['icon' => 'fa-people-group', 'text' => 'Kompetisi antar anggota'],
        ['icon' => 'fa-trophy', 'text' => 'Leaderboard & achievement'],
        ],
    ])
@endsection