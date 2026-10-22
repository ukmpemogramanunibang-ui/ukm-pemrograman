@extends('layouts.roles.owner')

@section('title', 'Import Anggota – Owner UNICODE')
@section('page-title', 'Import Anggota Massal')

@push('styles')
<style>
    .dropzone {
        border: 2px dashed #d1d5db;
        border-radius: 1rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    .dropzone:hover, .dropzone.dragover {
        border-color: #6366f1;
        background: #f3f4f6;
    }
    .dark .dropzone {
        border-color: #4b5563;
    }
    .dark .dropzone:hover, .dark .dropzone.dragover {
        border-color: #818cf8;
        background: #1f2937;
    }
    .dropzone i {
        font-size: 3rem;
        color: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 md:p-8 border border-gray-200/60 dark:border-gray-700/60">
        <div class="flex items-center gap-3 mb-6">
            <i class="fas fa-file-upload text-indigo-500 text-2xl"></i>
            <div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Upload File Anggota</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Support Excel, PDF, atau Word. Maks 10MB.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-4 text-green-700 dark:text-green-300">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4 text-red-700 dark:text-red-300">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('owner.management.members.import.preview') }}" method="POST" enctype="multipart/form-data" id="import-form">
            @csrf
            <div class="dropzone" id="dropzone">
                <input type="file" name="file" id="file-input" class="hidden" accept=".xlsx,.xls,.doc,.docx,.pdf">
                <i class="fas fa-cloud-upload-alt"></i>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Seret file ke sini atau klik untuk upload</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: XLSX, XLS, DOC, DOCX, PDF</p>
            </div>

            <div class="mt-4 flex justify-end gap-3">
                <a href="{{ route('owner.management.members.index') }}" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg hover:shadow-md transition">
                    <i class="fas fa-eye mr-2"></i> Preview Data
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const form = document.getElementById('import-form');

    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        // Submit otomatis setelah file di-drop
        form.submit();
    });
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            form.submit();
        }
    });
</script>
@endpush
@endsection