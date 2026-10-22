@extends('layouts.app')

@section('title', $unit['name'] . ' — UKM UNICODE')

@section('content')
    @include('partials.directory.detail', [
        'unit'       => $unit,
        'allItems'   => $allItems,
        'unitType'   => 'Unit Kegiatan Mahasiswa',
        'indexRoute' => 'ukm.index',
        'showRoute'  => 'ukm.show',
    ])
@endsection