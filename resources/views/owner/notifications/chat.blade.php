@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', 'Chat – UNICODE')
@section('page-title', 'Chat')

@section('content')
<div class="p-6 h-[calc(100vh-10rem)]">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden h-full shadow-lg flex flex-col">

        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('owner.notifications.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Percakapan</h2>
                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $conversations->count() ?? 0 }})</span>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-circle text-green-500 mr-1"></i> {{ auth()->user()->is_online ? 'Online' : 'Offline' }}
            </div>
        </div>

        {{-- Body – 2 kolom --}}
        <div class="flex flex-1 overflow-hidden">
            {{-- Daftar percakapan --}}
            <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-y-auto p-2">
                @if(isset($conversations) && $conversations->count())
                    @foreach($conversations as $conv)
                        @php
                            $otherUser = $conv->users->where('id', '!=', auth()->id())->first();
                            $lastMsg = $conv->messages->last();
                        @endphp
                        <a href="{{ route('owner.chats.private', $otherUser->user_uuid ?? '') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer {{ request()->route('uuid') == ($otherUser->user_uuid ?? '') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <img src="{{ $otherUser->avatar_url ? asset('storage/'.$otherUser->avatar_url) : asset('images/default-avatar.png') }}" class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $otherUser->name }}</p>
                                    <span class="text-xs text-gray-400">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $lastMsg->message ?? 'Belum ada pesan' }}</p>
                            </div>
                            @if($conv->unread_count > 0)
                                <span class="w-5 h-5 bg-blue-500 text-white text-[10px] rounded-full flex items-center justify-center">{{ $conv->unread_count }}</span>
                            @endif
                        </a>
                    @endforeach
                @else
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-6">Belum ada percakapan.</div>
                @endif
            </div>

            {{-- Area percakapan aktif --}}
            <div class="flex-1 flex flex-col">
                @if(isset($activeConversation) && $activeConversation)
                    @php $otherUser = $activeConversation->users->where('id', '!=', auth()->id())->first(); @endphp
                    {{-- Header percakapan aktif --}}
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                        <img src="{{ $otherUser->avatar_url ? asset('storage/'.$otherUser->avatar_url) : asset('images/default-avatar.png') }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $otherUser->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-circle {{ $otherUser->is_online ? 'text-green-500' : 'text-gray-400' }} mr-1"></i>
                                {{ $otherUser->is_online ? 'Online' : 'Offline' }}
                                @if(!$otherUser->is_online && $otherUser->last_seen_at)
                                    • Terakhir aktif {{ $otherUser->last_seen_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Pesan --}}
                    <div class="flex-1 p-4 overflow-y-auto space-y-3" id="message-container">
                        @foreach($activeMessages ?? [] as $msg)
                            <div class="flex items-start gap-2 {{ $msg->sender_id == auth()->id() ? 'justify-end' : '' }}">
                                @if($msg->sender_id != auth()->id())
                                    <img src="{{ $msg->sender->avatar_url ? asset('storage/'.$msg->sender->avatar_url) : asset('images/default-avatar.png') }}" class="w-8 h-8 rounded-full object-cover">
                                @endif
                                <div class="{{ $msg->sender_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} rounded-lg px-4 py-2 max-w-[70%] text-sm">
                                    <p>{{ $msg->message }}</p>
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="text-[10px] {{ $msg->sender_id == auth()->id() ? 'text-blue-200' : 'text-gray-400' }}">
                                            {{ $msg->created_at->format('H:i') }}
                                        </span>
                                        @if($msg->sender_id == auth()->id())
                                            <span class="text-[10px] {{ $msg->read_at ? 'text-blue-200' : 'text-gray-400' }}">
                                                {{ $msg->read_at ? '✓✓' : '✓' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($msg->sender_id == auth()->id())
                                    <img src="{{ auth()->user()->avatar_url ? asset('storage/'.auth()->user()->avatar_url) : asset('images/default-avatar.png') }}" class="w-8 h-8 rounded-full object-cover">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Input pesan --}}
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <form id="chat-form" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
                            <input type="text" name="message" id="chat-input" placeholder="Tulis pesan..." class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-md shadow-blue-500/20">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-gray-400 dark:text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-comments text-4xl mb-3 block opacity-30"></i>
                            <p>Pilih percakapan atau mulai chat dari notifikasi.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto scroll ke bawah
    const container = document.getElementById('message-container');
    if (container) container.scrollTop = container.scrollHeight;

    // Submit form chat dengan AJAX
    document.getElementById('chat-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;

        const formData = new FormData(this);
        fetch('{{ route("owner.chats.send") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                // Append pesan baru tanpa reload
                const msgDiv = document.createElement('div');
                msgDiv.className = 'flex items-start gap-2 justify-end';
                msgDiv.innerHTML = `
                    <div class="bg-blue-600 text-white rounded-lg px-4 py-2 max-w-[70%] text-sm">
                        <p>${message}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-[10px] text-blue-200">${new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'})}</span>
                            <span class="text-[10px] text-blue-200">✓</span>
                        </div>
                    </div>
                    <img src="{{ auth()->user()->avatar_url ? asset('storage/'.auth()->user()->avatar_url) : asset('images/default-avatar.png') }}" class="w-8 h-8 rounded-full object-cover">
                `;
                container.appendChild(msgDiv);
                container.scrollTop = container.scrollHeight;
            }
        })
        .catch(err => console.warn(err));
    });
</script>
@endpush
@endsection