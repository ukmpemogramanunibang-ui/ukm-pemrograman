@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Pilih Organisasi</h1>
    <div class="grid gap-3">
        @forelse ($organizations as $org)
            <form method="POST" action="{{ route('organization.switch', $org) }}">
                @csrf
                <button class="w-full text-left p-4 bg-white rounded-xl border hover:border-purple-500">
                    <div class="font-semibold">{{ $org->name }}</div>
                    <div class="text-sm text-gray-500">
                        {{ ucfirst($org->type) }} · {{ $org->pivot->access_role }} · {{ $org->pivot->position_label ?? '—' }}
                    </div>
                </button>
            </form>
        @empty
            <p class="text-gray-500">Anda belum bergabung ke organisasi mana pun.</p>
        @endforelse
    </div>
</div>
@endsection