@extends('layouts.public')

@section('title', 'Booking '.$room->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-10">
    <div class="container mx-auto px-4 max-w-3xl">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

            <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white p-6">
                <h1 class="text-2xl font-bold"><i class="fas fa-calendar-plus mr-2"></i> Booking Ruangan</h1>
                <p class="text-cyan-100 text-sm mt-1">
                    {{ $room->room_code }} – {{ $room->name }} ({{ $room->building ?? '-' }})
                </p>
            </div>

            @if(session('error'))
                <div class="m-4 p-4 bg-red-50 border-l-4 border-red-500 rounded text-red-800">
                    {{ session('error') }}
                    @if(session('conflict'))
                        <p class="text-sm mt-1">
                            Bentrok: {{ session('conflict')->start_datetime->format('H:i') }} –
                            {{ session('conflict')->end_datetime->format('H:i') }}
                        </p>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('room.public.booking.store', $room) }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Jenis Pemesan
                    </label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['mahasiswa','dosen','panitia','umum'] as $t)
                            <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer
                                          has-[:checked]:bg-cyan-50 has-[:checked]:border-cyan-500">
                                <input type="radio" name="requester_type" value="{{ $t }}"
                                       @checked(old('requester_type') === $t) required>
                                <span class="capitalize">{{ $t }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">NIM / NIDN / ID</label>
                        <input type="text" name="requester_id" value="{{ old('requester_id') }}" required
                               class="w-full px-3 py-2 rounded-lg border ...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Pemesan</label>
                        <input type="text" name="requester_name" value="{{ old('requester_name') }}" required
                               class="w-full px-3 py-2 rounded-lg border ...">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nomor HP</label>
                        <input type="text" name="requester_phone" value="{{ old('requester_phone') }}" required
                               class="w-full px-3 py-2 rounded-lg border ...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jumlah Peserta</label>
                        <input type="number" name="participants" value="{{ old('participants', 10) }}" required
                               max="{{ $room->capacity_normal }}"
                               class="w-full px-3 py-2 rounded-lg border ...">
                        <p class="text-xs text-gray-500 mt-1">Maksimal {{ $room->capacity_normal }} orang</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Keperluan</label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}" required
                           placeholder="Rapat organisasi, seminar, dll"
                           class="w-full px-3 py-2 rounded-lg border ...">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Acara (opsional)</label>
                    <input type="text" name="event_name" value="{{ old('event_name') }}"
                           class="w-full px-3 py-2 rounded-lg border ...">
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Tanggal</label>
                        <input type="date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 rounded-lg border ...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jam Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required
                               class="w-full px-3 py-2 rounded-lg border ...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jam Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time', '11:00') }}" required
                               class="w-full px-3 py-2 rounded-lg border ...">
                    </div>
                </div>

                <button type="button" id="btnCheck"
                        class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition">
                    <i class="fas fa-search mr-2"></i> CEK KETERSEDIAAN
                </button>

                <div id="checkResult" class="hidden p-3 rounded-lg text-sm"></div>

                <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i> KIRIM PERMOHONAN BOOKING
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('btnCheck').addEventListener('click', async function () {
    const form = this.closest('form');
    const res = document.getElementById('checkResult');

    const payload = {
        room_id:      {{ $room->id }},
        booking_date: form.booking_date.value,
        start_time:   form.start_time.value,
        end_time:     form.end_time.value,
        _token:       '{{ csrf_token() }}',
    };

    res.classList.remove('hidden');
    res.className = 'p-3 rounded-lg text-sm bg-gray-100 text-gray-700';
    res.textContent = 'Memeriksa ketersediaan...';

    try {
        const r = await fetch('{{ route('room.public.check') }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify(payload),
        });
        const data = await r.json();

        if (data.available) {
            res.className = 'p-3 rounded-lg text-sm bg-green-50 text-green-800 border border-green-300';
            res.innerHTML = '✅ <strong>Ruangan tersedia</strong> pada waktu tersebut.';
        } else {
            res.className = 'p-3 rounded-lg text-sm bg-red-50 text-red-800 border border-red-300';
            let msg = '❌ Ruangan <strong>tidak tersedia</strong>.';
            if (data.conflict) {
                msg += `<br>Bentrok dengan jadwal ${data.conflict.start} – ${data.conflict.end} (${data.conflict.by}).`;
            }
            res.innerHTML = msg;
        }
    } catch (e) {
        res.className = 'p-3 rounded-lg text-sm bg-red-100 text-red-700';
        res.textContent = 'Gagal memeriksa. Coba lagi.';
    }
});
</script>
@endsection