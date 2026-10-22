@extends('layouts.roles.owner')

@section('title', 'Pelanggaran Berat – Owner UNICODE')
@section('page-title', 'Pelanggaran Berat')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'heavy',
        'pageTitle' => 'Pelanggaran Berat',
        'pageDesc'  => 'Anggota dengan pelanggaran tingkat berat.',
        'pageIcon'  => 'fa-fire',
    ])
@endsection