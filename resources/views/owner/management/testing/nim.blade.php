@extends('layouts.roles.owner')
@section('title', 'NIM Testing – Owner')
@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">NIM Testing</h1>
        <a href="{{ route('owner.management.testing.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">Kembali</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Masukkan NIM</label>
        <div class="flex gap-2 mt-2">
            <input type="text" id="nimInput" placeholder="Contoh: 2310xxxxxx" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-cyan">
            <button onclick="findUser()" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">Cari</button>
        </div>
        <div id="nimResult" class="mt-4"></div>
    </div>
</div>
<script>
function findUser() {
    const nim = document.getElementById('nimInput').value.trim();
    if (!nim) { alert('Masukkan NIM.'); return; }
    fetch('{{ route("owner.management.testing.nim.find") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ nim })
    })
    .then(res => res.json())
    .then(data => {
        const div = document.getElementById('nimResult');
        if (data.found) {
            div.innerHTML = `<pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">${JSON.stringify(data.user, null, 2)}</pre>`;
        } else {
            div.innerHTML = `<div class="bg-red-50 text-red-700 p-4 rounded-lg">${data.message}</div>`;
        }
    });
}
</script>
@endsection