@extends('layouts.roles.owner')
@section('title', 'Deep Validation – Owner')
@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Deep Security Validation</h1>
        <a href="{{ route('owner.management.testing.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 transition">Kembali</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">QR Payload / UUID</label>
        <div class="flex gap-2 mt-2">
            <input type="text" id="validateInput" placeholder="https://... atau UUID" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-red-500">
            <button onclick="deepValidate()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Validate</button>
        </div>
        <div id="validateResult" class="mt-4"></div>
    </div>
</div>
<script>
function deepValidate() {
    const payload = document.getElementById('validateInput').value.trim();
    if (!payload) { alert('Masukkan payload.'); return; }
    fetch('{{ route("owner.management.testing.validate.deep") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ payload })
    })
    .then(res => res.json())
    .then(data => {
        const div = document.getElementById('validateResult');
        div.innerHTML = `<pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">${JSON.stringify(data, null, 2)}</pre>`;
    });
}
</script>
@endsection