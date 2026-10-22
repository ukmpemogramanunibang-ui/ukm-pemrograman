@extends('layouts.roles.owner')

@section('title', 'Pelanggaran Ringan – Owner UNICODE')
@section('page-title', 'Pelanggaran Ringan')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'light',
        'pageTitle' => 'Pelanggaran Ringan',
        'pageDesc'  => 'Anggota dengan pelanggaran tingkat ringan.',
        'pageIcon'  => 'fa-info-circle',
    ])
@endsection