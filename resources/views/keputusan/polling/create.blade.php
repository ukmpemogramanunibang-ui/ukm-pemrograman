@extends('layouts.app')

@section('title', 'Buat Polling')

@section('content')
<section class="min-h-screen bg-gradient-to-br from-midnight via-deepblue to-electric py-16">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 p-8">
            <h1 class="text-3xl font-extrabold text-white mb-2">🗳️ Buat Polling</h1>
            <p class="text-white/60 mb-8">Buat pilihan, bagikan tautan, dan lihat hasil suara live.</p>

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/40 text-red-100 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('keputusan.poll.store') }}" class="space-y-6" id="poll-form">
                @csrf

                <div>
                    <label class="block text-white font-semibold mb-2">Judul Polling</label>
                    <input type="text" name="title" required maxlength="150"
                           class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white outline-none focus:border-cyan-400">
                </div>

                <div>
                    <label class="block text-white font-semibold mb-2">Deskripsi (opsional)</label>
                    <textarea name="description" rows="2" maxlength="500"
                              class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white outline-none focus:border-cyan-400"></textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-white font-semibold">Pilihan</label>
                        <span class="text-white/60 text-sm"><span id="p-count">2</span> / 20</span>
                    </div>
                    <div id="options" class="space-y-3"></div>
                    <button type="button" id="add-opt"
                            class="mt-3 w-full py-3 rounded-xl border-2 border-dashed border-white/20 text-white/70 hover:border-violet-400 hover:text-violet-300 transition">
                        + Tambah Pilihan
                    </button>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <label class="flex items-center gap-3 text-white">
                        <input type="checkbox" name="allow_multiple" value="1" class="w-5 h-5">
                        Boleh pilih lebih dari satu
                    </label>
                    <div>
                        <label class="block text-white font-semibold mb-2">Berakhir Pada</label>
                        <input type="datetime-local" name="closes_at"
                               class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white outline-none">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-4 rounded-2xl bg-gradient-to-r from-violet to-electric text-white font-extrabold text-lg hover:shadow-[0_0_40px_rgba(139,92,246,0.5)] transition">
                    🚀 BUAT POLLING
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const box = document.getElementById('options');
    const add = document.getElementById('add-opt');
    const cnt = document.getElementById('p-count');
    const MAX = 20;

    function row(val='') {
        const idx = box.children.length;
        const d = document.createElement('div');
        d.className = 'flex gap-2';
        d.innerHTML = `
            <input type="text" name="options[${idx}]" value="${val}" placeholder="Tulis pilihan"
                   class="flex-1 rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white outline-none focus:border-violet-400">
            <button type="button" class="remove px-3 rounded-xl bg-red-500/20 text-red-200 hover:bg-red-500/40">✕</button>
        `;
        d.querySelector('.remove').onclick = () => { d.remove(); reindex(); };
        box.appendChild(d);
        update();
    }
    function reindex() {
        [...box.children].forEach((r,i) => {
            r.querySelector('input').name = `options[${i}]`;
        });
        update();
    }
    function update() {
        cnt.textContent = box.children.length;
        add.disabled = box.children.length >= MAX;
    }
    add.onclick = () => { if (box.children.length < MAX) row(); };
    ['Setuju','Tidak Setuju'].forEach(v => row(v));
});
</script>
@endsection