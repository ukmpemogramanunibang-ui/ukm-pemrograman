{{-- resources/views/anggota/sanksi.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Sanksi — Anggota')
@section('breadcrumb', 'Sanksi')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Sanksi',
        'icon'        => 'fa-triangle-exclamation',
        'tone'        => 'rose',
        'description' => 'Lihat riwayat sanksi dan status kedisiplinan Anda.',
        'progress'    => 20,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-list', 'text' => 'Riwayat sanksi pribadi'],
        ['icon' => 'fa-circle-exclamation', 'text' => 'Detail pelanggaran'],
        ['icon' => 'fa-gavel', 'text' => 'Status banding & upaya'],
        ['icon' => 'fa-star-half-stroke', 'text' => 'Point pengurangan & pemulihan'],
        ],
    ])
@endsection