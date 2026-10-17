{{-- resources/views/anggota/divisi.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Divisi Saya — Anggota')
@section('breadcrumb', 'Divisi Saya')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Divisi Saya',
        'icon'        => 'fa-sitemap',
        'tone'        => 'indigo',
        'description' => 'Akses divisi yang Anda ikuti dan kelola kontribusi Anda.',
        'progress'    => 35,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-circle-info', 'text' => 'Profil & struktur divisi'],
        ['icon' => 'fa-diagram-project', 'text' => 'Proyek & progress divisi'],
        ['icon' => 'fa-file-lines', 'text' => 'Rapat, notulen, dan dokumen'],
        ['icon' => 'fa-users', 'text' => 'Anggota divisi Anda'],
        ],
    ])
@endsection