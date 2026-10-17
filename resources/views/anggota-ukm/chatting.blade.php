{{-- resources/views/anggota/chatting.blade.php --}}
@extends('layouts.roles.anggota-ukm')

@section('title', 'Chatting — Anggota')
@section('breadcrumb', 'Chatting')

@section('content')
    @include('anggota._coming-soon', [
        'title'       => 'Chatting',
        'icon'        => 'fa-comments',
        'tone'        => 'cyan',
        'description' => 'Chat realtime dengan anggota, pengurus, dan kelompok Anda.',
        'progress'    => 45,
        'eta'         => 'Segera Hadir',
        'features'    => [
        ['icon' => 'fa-comment-dots', 'text' => 'Chat pribadi 1-on-1 dengan siapa saja'],
        ['icon' => 'fa-users', 'text' => 'Chat grup per kelompok dan divisi'],
        ['icon' => 'fa-image', 'text' => 'Kirim gambar, file, dan voice note'],
        ['icon' => 'fa-bell', 'text' => 'Notifikasi pesan baru realtime'],
        ],
    ])
@endsection