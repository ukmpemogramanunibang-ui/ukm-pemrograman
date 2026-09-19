@extends('layouts.app')

@section('title', 'Memproses Login – UKM Pemrograman UNICODE')

@push('styles')
    <style>
        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.2);
            border-top-color: #06b6d4;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0B1120] via-[#1E2A4A] to-[#06B6D4]">
    <div class="text-center text-white p-8" data-aos="fade-up">
        <div class="loader mx-auto mb-6"></div>
        <h2 class="text-3xl font-bold mb-2">Sedang Memproses Login</h2>
        <p class="text-lg opacity-80">Mohon tunggu sebentar, Anda akan dialihkan...</p>
    </div>
</div>

@if(isset($redirect))
    <script>
        setTimeout(function() {
            window.location.href = "{{ $redirect }}";
        }, 2000);
    </script>
@else
    <script>
        setTimeout(function() {
            window.location.href = "{{ url('/home') }}";
        }, 2000);
    </script>
@endif
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 500, once: true });
</script>
@endpush