@extends('layouts.app')

@section('title', $unit['name'] . ' — Organisasi UNICODE')

@section('content')
    @include('partials.directory.detail', [
        'unit'       => $unit,
        'allItems'   => $allItems,
        'unitType'   => 'Organisasi',
        'indexRoute' => 'organisasi.index',
        'showRoute'  => 'organisasi.show',
    ])
@endsection