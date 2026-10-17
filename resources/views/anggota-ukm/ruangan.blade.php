{{-- resources/views/anggota/ruangan.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Ruangan — Anggota')
@section('breadcrumb', 'Ruangan')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Ruangan',
        'icon'        => 'fa-door-open',
        'tone'        => 'sky',
        'description' => 'Cari, lihat detail, dan booking ruangan yang tersedia.',
        'progress'    => 50,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-circle-check', 'text' => 'Cek ketersediaan realtime'],
        ['icon' => 'fa-calendar-plus', 'text' => 'Booking ruangan online'],
        ['icon' => 'fa-clock-rotate-left', 'text' => 'Riwayat booking pribadi'],
        ['icon' => 'fa-qrcode', 'text' => 'QR check-in & check-out'],
        ],
    ])
@endsection