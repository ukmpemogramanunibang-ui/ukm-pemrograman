@extends('layouts.app') <!-- sesuaikan dengan layout Anda -->
@section('content')
<div class="container">
    <h2>Informasi Ruangan</h2>
    <p>Nama: {{ $room->name }}</p>
    <p>Kode: {{ $room->room_code }}</p>
    <p>Gedung: {{ $room->building ?? '-' }}</p>
    <p>Lantai: {{ $room->floor ?? '-' }}</p>
</div>
@endsection