@extends('layouts.roles.owner')

@section('title', 'Manajemen Pelanggaran – Owner UNICODE')
@section('page-title', 'Manajemen Pelanggaran')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'index',
        'pageTitle' => 'Manajemen Pelanggaran',
        'pageDesc'  => 'Ringkasan seluruh anggota dengan catatan pelanggaran / status non-aktif.',
        'pageIcon'  => 'fa-shield-alt',
    ])
@endsection