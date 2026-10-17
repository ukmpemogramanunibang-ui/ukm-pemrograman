<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Organisasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pilih Organisasi</h1>
            <a href="{{ route('dashboard') }}" class="text-sm text-purple-600 hover:underline">← Dashboard</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-3">
            @forelse ($organizations as $org)
                <form method="POST" action="{{ route('organization.switch', $org) }}">
                    @csrf
                    <button type="submit" class="w-full text-left p-4 bg-white rounded-xl border-2 border-gray-200 hover:border-purple-500 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $org->name }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ ucfirst($org->type) }} · {{ $org->pivot->access_role }} · {{ $org->pivot->position_label ?? '—' }}
                                </div>
                            </div>
                            <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">{{ ucfirst($org->type) }}</span>
                        </div>
                    </button>
                </form>
            @empty
                <div class="p-6 bg-white rounded-xl border border-gray-200 text-center text-gray-500">
                    Anda belum bergabung ke organisasi mana pun.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>