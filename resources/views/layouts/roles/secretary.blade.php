{{-- resources/views/layouts/roles/secretary.blade.php --}}
@extends('layouts.app')

@section('sidebar')
    <div class="p-4">
        <h2 class="text-xl font-bold text-blue-600 mb-4">Sekretaris</h2>
        <ul class="space-y-2">
            <li><a href="{{ route('owner.officials.secretary') }}" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Dashboard</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Chat Sekretaris</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Tugas</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Pengumuman</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Agenda & Jadwal</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Surat & Dokumen</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Drive Sekretaris</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Arsip</a></li>
            <li><a href="#" class="block p-2 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30">Riwayat Aktivitas</a></li>
        </ul>
    </div>
@endsection