@extends('layouts.app')

@section('title', 'Direktori UKM — UNICODE')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    @include('partials.directory.page-header', [
        'title'    => 'Direktori UKM',
        'subtitle' => 'Unit Kegiatan Mahasiswa Universitas Bhakti Asih.',
        'count'    => count($items),
        'icon'     => 'fas fa-users-rectangle',
    ])

    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 pt-12 sm:px-6 lg:px-10">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($items as $item)
                @include('partials.directory.card', [
                    'item'      => $item,
                    'showRoute' => 'ukm.' . $item['slug'],
                ])
            @endforeach
        </div>
    </section>
</div>
@endsection