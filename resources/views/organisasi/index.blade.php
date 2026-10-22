@extends('layouts.app')

@section('title', 'Direktori Organisasi — UNICODE')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-midnight via-deepblue to-midnight text-white">

    @include('partials.directory.page-header', [
        'title'    => 'Direktori Organisasi',
        'subtitle' => 'Kumpulan organisasi mahasiswa Universitas Bhakti Asih.',
        'count'    => count($items),
        'icon'     => 'fas fa-sitemap',
    ])

    <section class="mx-auto w-full max-w-[1800px] px-4 pb-20 pt-12 sm:px-6 lg:px-10">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($items as $item)
                @include('partials.directory.card', [
                    'item'      => $item,
                    'showRoute' => 'organisasi.' . $item['slug'],   {{-- ◄ route spesifik per unit --}}
                ])
            @endforeach
        </div>
    </section>
</div>
@endsection