@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', 'Buat Notulen – Sekretaris UNICODE')
@section('page-title', 'Buat Notulen')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto">

    <form action="{{ route('owner.officials.secretary.minutes.store') }}"
          method="POST" enctype="multipart/form-data" id="minutesForm" class="space-y-6">

        @csrf

        {{-- ========== HEADER ========== --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                        <i class="fas fa-sticky-note text-yellow-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Buat Notulen Rapat</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Dokumentasikan hasil rapat secara profesional. Hubungkan dengan booking untuk mengisi data otomatis.
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('owner.officials.secretary.minutes.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- ========== STEP 1: SUMBER RAPAT ========== --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-link text-blue-500"></i>
                    Hubungkan dengan Data Rapat
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih booking yang sudah ada untuk mengisi data otomatis, atau buat manual.</p>
            </div>
            <div class="p-6 space-y-4">

                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <input type="radio" name="source_type" value="booking" id="sourceBooking" checked
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <i class="fas fa-calendar-check text-blue-500"></i> Booking Ruangan
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <input type="radio" name="source_type" value="manual" id="sourceManual"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <i class="fas fa-pen text-gray-500"></i> Manual (Tanpa Booking)
                    </label>
                </div>

                <div id="bookingSelector" class="space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Pilih Booking
                        </label>
                        <select id="bookingSelect" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Pilih Booking --</option>
                            @foreach($bookings ?? [] as $booking)
                                <option value="{{ $booking->id }}">
                                    {{ $booking->title }} – {{ $booking->room->name }} ({{ $booking->start_datetime->format('d/m/Y H:i') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Booking yang sudah disetujui dan akan datang.</p>
                    </div>

                    <div id="bookingPreview" class="hidden p-4 rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/10">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                            <div class="flex-1 text-sm">
                                <p class="font-semibold text-blue-800 dark:text-blue-300" id="previewTitle">Rapat Pengurus</p>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 mt-1 text-gray-700 dark:text-gray-300">
                                    <span><span class="font-medium">Tanggal:</span> <span id="previewDate">-</span></span>
                                    <span><span class="font-medium">Waktu:</span> <span id="previewTime">-</span></span>
                                    <span><span class="font-medium">Ruangan:</span> <span id="previewRoom">-</span></span>
                                    <span><span class="font-medium">Pemesan:</span> <span id="previewRequester">-</span></span>
                                </div>
                                <div id="previewParticipants" class="mt-1 text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Peserta:</span> <span id="previewParticipantCount">-</span> orang
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== STEP 2-9: SAMA SEPERTI SEBELUMNYA ========== --}}
        {{-- Silakan gunakan kode dari jawaban sebelumnya untuk bagian Informasi Rapat, Peserta, Agenda, Pembahasan, Keputusan, Tindak Lanjut, Lampiran, Status --}}
        {{-- ... (kode lengkap sudah saya berikan di jawaban sebelumnya) ... --}}

        {{-- ========== TOMBOL ========== --}}
        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('owner.officials.secretary.minutes.index') }}"
               class="inline-flex justify-center items-center px-5 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl transition">
                Batal
            </a>
            <button type="submit" id="submitBtn"
                    class="inline-flex justify-center items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl shadow-lg shadow-yellow-500/20 transition">
                <i class="fas fa-save mr-2"></i> Simpan Notulen
            </button>
        </div>

    </form>
</div>

{{-- ============================================================ --}}
{{-- JAVASCRIPT LENGKAP UNTUK INTEGRASI BOOKING --}}
{{-- ============================================================ --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ---- ELEMEN ---- //
        const sourceBooking = document.getElementById('sourceBooking');
        const sourceManual = document.getElementById('sourceManual');
        const bookingSelector = document.getElementById('bookingSelector');
        const bookingSelect = document.getElementById('bookingSelect');
        const bookingPreview = document.getElementById('bookingPreview');
        const bookingIdField = document.getElementById('bookingIdField');

        const fieldTitle = document.getElementById('fieldTitle');
        const fieldDate = document.getElementById('fieldDate');
        const fieldTime = document.getElementById('fieldTime');
        const fieldEndTime = document.getElementById('fieldEndTime');
        const fieldLocation = document.getElementById('fieldLocation');
        const fieldLeader = document.getElementById('fieldLeader');
        const fieldParticipantCount = document.getElementById('fieldParticipantCount');
        const fieldParticipants = document.getElementById('fieldParticipants');
        const fieldAgenda = document.getElementById('fieldAgenda');
        const fieldDiscussion = document.getElementById('fieldDiscussion');
        const fieldDecisions = document.getElementById('fieldDecisions');
        const fieldFollowUp = document.getElementById('fieldFollowUp');

        const previewTitle = document.getElementById('previewTitle');
        const previewDate = document.getElementById('previewDate');
        const previewTime = document.getElementById('previewTime');
        const previewRoom = document.getElementById('previewRoom');
        const previewRequester = document.getElementById('previewRequester');
        const previewParticipantCount = document.getElementById('previewParticipantCount');

        // ---- TOGGLE SUMBER ---- //
        function toggleSource() {
            if (sourceBooking.checked) {
                bookingSelector.style.display = 'block';
                if (bookingSelect.value) {
                    fetchBookingData(bookingSelect.value);
                } else {
                    bookingPreview.classList.add('hidden');
                }
            } else {
                bookingSelector.style.display = 'none';
                bookingPreview.classList.add('hidden');
            }
        }

        sourceBooking.addEventListener('change', toggleSource);
        sourceManual.addEventListener('change', toggleSource);
        toggleSource();

        // ---- FETCH DATA BOOKING ---- //
        function fetchBookingData(bookingId) {
            if (!bookingId) {
                bookingPreview.classList.add('hidden');
                return;
            }

            bookingPreview.classList.remove('hidden');
            previewTitle.textContent = '⏳ Memuat data...';

            fetch('{{ url("owner/officials/secretary/minutes/booking-data") }}/' + bookingId, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    previewTitle.textContent = '❌ Gagal memuat data';
                    previewTitle.className = 'text-red-500';
                    return;
                }
                previewTitle.className = 'font-semibold text-blue-800 dark:text-blue-300';
                const d = data.data;
                previewTitle.textContent = d.title;
                previewDate.textContent = d.meeting_date;
                previewTime.textContent = d.meeting_time + (d.end_time ? ' - ' + d.end_time : '');
                previewRoom.textContent = d.location;
                previewRequester.textContent = d.requester_name || '-';
                previewParticipantCount.textContent = d.participants || 0;

                bookingIdField.value = bookingId;
                fieldTitle.value = d.title;
                fieldDate.value = d.meeting_date;
                fieldTime.value = d.meeting_time;
                fieldEndTime.value = d.end_time || '';
                fieldLocation.value = d.location;
                fieldParticipantCount.value = d.participants || 0;
                if (d.participants_list) {
                    fieldParticipants.value = d.participants_list;
                }
            })
            .catch(err => {
                console.error(err);
                previewTitle.textContent = '⚠️ Error memuat data';
                previewTitle.className = 'text-red-500';
            });
        }

        bookingSelect.addEventListener('change', function() {
            if (this.value) {
                fetchBookingData(this.value);
            } else {
                bookingPreview.classList.add('hidden');
                bookingIdField.value = '';
                fieldTitle.value = '';
                fieldDate.value = '';
                fieldTime.value = '';
                fieldEndTime.value = '';
                fieldLocation.value = '';
                fieldParticipantCount.value = '';
            }
        });

        // ---- PREVIEW FILE UPLOAD ---- //
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('filePreviewContainer');

        fileInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            const files = this.files;
            if (files.length === 0) {
                previewContainer.innerHTML = '<span class="text-xs text-gray-400">Belum ada file dipilih</span>';
                return;
            }
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-full text-sm';
                const icon = document.createElement('i');
                const ext = file.name.split('.').pop().toLowerCase();
                if (['pdf'].includes(ext)) icon.className = 'fas fa-file-pdf text-red-500';
                else if (['doc','docx'].includes(ext)) icon.className = 'fas fa-file-word text-blue-600';
                else if (['jpg','jpeg','png','webp'].includes(ext)) icon.className = 'fas fa-file-image text-purple-500';
                else icon.className = 'fas fa-file text-gray-500';
                const span = document.createElement('span');
                span.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                div.appendChild(icon);
                div.appendChild(span);
                previewContainer.appendChild(div);
            }
        });

        // ---- VALIDASI FORM ---- //
        document.getElementById('minutesForm').addEventListener('submit', function(e) {
            const title = fieldTitle.value.trim();
            const date = fieldDate.value;
            const time = fieldTime.value;
            const location = fieldLocation.value.trim();
            if (!title || !date || !time || !location) {
                e.preventDefault();
                alert('Harap isi semua field yang wajib (Judul, Tanggal, Waktu, Tempat).');
                return false;
            }
        });

        // Jika ada old value booking_id, load otomatis
        @if(old('booking_id'))
            bookingSelect.value = {{ old('booking_id') }};
            fetchBookingData({{ old('booking_id') }});
            sourceBooking.checked = true;
            toggleSource();
        @endif

    });
</script>
@endpush

@endsection