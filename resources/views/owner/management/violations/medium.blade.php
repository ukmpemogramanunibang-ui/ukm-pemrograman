@extends('layouts.roles.owner')

@section('title', 'Pelanggaran Sedang – Owner UNICODE')
@section('page-title', 'Pelanggaran Sedang')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'medium',
        'pageTitle' => 'Pelanggaran Sedang',
        'pageDesc'  => 'Anggota dengan pelanggaran tingkat sedang.',
        'pageIcon'  => 'fa-bolt',
    ])
@endsection