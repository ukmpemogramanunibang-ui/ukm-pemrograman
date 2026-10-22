@extends('layouts.public')

@section('title', 'Status Booking')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10">
    <div class="container mx-auto px-4 max-w-2xl">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                Booking Berhasil Dikirim
            </h1>
            <p class="text-gray-500 mb-6">Simpan kode booking berikut untuk pengecekan:</p>

            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl py-4 mb-6">
                <p class="text-3xl font-mono font-bold text-cyan-600 tracking-widest">
                    {{ $booking->booking_code }}
                </p>
            </div>

            <table class="w-full text-left text-sm">
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 text-gray-500">Ruangan</td>
                    <td class="py-2 text-right font-semibold">{{ $booking->room->name ?? '-' }}</td>
                </tr>
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 text-gray-500">Tanggal</td>
                    <td class="py-2 text-right font-semibold">
                        {{ $booking->start_datetime->format('d M Y') }}
                    </td>
                </tr>
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 text-gray-500">Waktu</td>
                    <td class="py-2 text-right font-semibold">
                        {{ $booking->start_datetime->format('H:i') }} –
                        {{ $booking->end_datetime->format('H:i') }}
                    </td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-500">Status</td>
                    <td class="py-2 text-right">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                            {{ strtoupper($booking->status) }}
                        </span>
                    </td>
                </tr>
            </table>

            <a href="{{ route('room.public.index') }}"
               class="mt-6 inline-block px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Ruangan
            </a>
        </div>
    </div>
</div>
@endsection