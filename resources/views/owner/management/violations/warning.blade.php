@extends('layouts.roles.owner')

@section('title', 'Teguran Tertulis – Owner UNICODE')
@section('page-title', 'Teguran Tertulis')

@section('content')
    @include('owner.management.violations._content', [
        'levelKey'  => 'warning',
        'pageTitle' => 'Teguran Tertulis',
        'pageDesc'  => 'Anggota dengan pelanggaran tingkat teguran tertulis.',
        'pageIcon'  => 'fa-exclamation-triangle',
    ])
@endsection