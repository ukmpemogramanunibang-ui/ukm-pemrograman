@extends('layouts.roles.owner')

@section('title', 'Diblokir – Owner UNICODE')
@section('page-title', 'Akun Diblokir')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'block',
        'pageTitle' => 'Akun Diblokir',
        'pageDesc'  => 'Anggota yang diblokir karena pelanggaran serius.',
        'pageIcon'  => 'fa-ban',
    ])
@endsection