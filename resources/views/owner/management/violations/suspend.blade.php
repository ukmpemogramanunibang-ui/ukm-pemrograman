@extends('layouts.roles.owner')

@section('title', 'Ditangguhkan – Owner UNICODE')
@section('page-title', 'Penangguhan Akun')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'suspend',
        'pageTitle' => 'Penangguhan Akun',
        'pageDesc'  => 'Anggota yang akunnya sedang ditangguhkan sementara.',
        'pageIcon'  => 'fa-pause-circle',
    ])
@endsection