{{-- resources/views/anggota/jadwal.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Jadwal — Anggota')
@section('breadcrumb', 'Jadwal')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Jadwal',
        'icon'        => 'fa-calendar-days',
        'tone'        => 'blue',
        'description' => 'Lihat dan kelola jadwal kegiatan pribadi maupun kelompok.',
        'progress'    => 55,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-calendar', 'text' => 'Kalender bulanan & mingguan'],
        ['icon' => 'fa-sync', 'text' => 'Sinkronisasi jadwal kelompok'],
        ['icon' => 'fa-bell', 'text' => 'Reminder via notifikasi'],
        ['icon' => 'fa-download', 'text' => 'Export ke Google Calendar'],
        ],
    ])
@endsection