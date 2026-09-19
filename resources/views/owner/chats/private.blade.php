@extends('layouts.roles.owner')

@section('title', 'Chat Privat – Owner')

@section('content')

@php
    $chatUser = $chatUser ?? null;
    $messages = $messages ?? collect();
    $conversation = $conversation ?? null;

    $userName = $chatUser->name ?? 'Pilih Kontak';
    $userNim = $chatUser->nim ?? '-';
    $userPhone = $chatUser->phone ?? '-';
    $userRole = $chatUser->level->name ?? 'Anggota';
    $userAvatar = $chatUser->avatar_url ?? null;
    $isOnline = $chatUser->is_online ?? false;
@endphp

<div class="h-[calc(100vh-130px)] min-h-[600px] p-4 md:p-6">

    <div class="h-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl
                border border-gray-200 dark:border-gray-700
                overflow-hidden flex flex-col lg:flex-row">

        {{-- ========================================================= --}}
        {{-- SIDEBAR KIRI: DAFTAR PERCAKAPAN --}}
        {{-- ========================================================= --}}
        <aside class="w-full lg:w-[320px] flex-shrink-0
                      border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-700
                      bg-gray-50 dark:bg-gray-900/50
                      flex flex-col max-h-[300px] lg:max-h-full">

            {{-- Header sidebar --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">
                    <i class="fas fa-comment-dots mr-2 text-cyan-500"></i> Percakapan
                </h3>
                <button onclick="openNewChat()" class="w-8 h-8 rounded-full bg-cyan-600 text-white flex items-center justify-center hover:bg-cyan-700 transition text-sm">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            {{-- Search --}}
            <div class="p-3">
                <div class="relative">
                    <input type="text" id="chatSearchList" placeholder="Cari kontak..."
                           class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-sm px-4 py-2 pl-9 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>

            {{-- Daftar percakapan --}}
            <div class="flex-1 overflow-y-auto p-2 space-y-1" id="conversationList">
                @forelse($conversations ?? [] as $conv)
                    @php
                        $otherUser = $conv->participants->where('user_id', '!=', auth()->id())->first()?->user;
                        $lastMsg = $conv->lastMessage;
                        $isActive = $conversation && $conversation->id == $conv->id;
                        $unread = $conv->messages()->where('sender_id', '!=', auth()->id())->where('status', '!=', 'read')->count();
                    @endphp
                    @if($otherUser)
                    <a href="{{ route('owner.chats.private', $otherUser->user_uuid) }}"
                       class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-gray-200 dark:hover:bg-gray-700/50 {{ $isActive ? 'bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800' : '' }}">
                        <div class="relative flex-shrink-0">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800 {{ $otherUser->is_online ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $otherUser->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $lastMsg ? Str::limit($lastMsg->message, 30) : 'Belum ada pesan' }}
                            </p>
                        </div>
                        @if($unread > 0)
                            <span class="w-5 h-5 rounded-full bg-cyan-500 text-white text-xs flex items-center justify-center flex-shrink-0">{{ $unread }}</span>
                        @endif
                    </a>
                    @endif
                @empty
                    <div class="text-center text-gray-400 text-sm py-8">
                        <i class="fas fa-inbox text-2xl block mb-2"></i>
                        Belum ada percakapan.
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- ========================================================= --}}
        {{-- RUANG CHAT --}}
        {{-- ========================================================= --}}
        <div class="flex-1 flex flex-col min-h-0">

            @if($chatUser)

                {{-- HEADER CHAT --}}
                <div class="h-[72px] flex-shrink-0
                            bg-white dark:bg-gray-800
                            border-b border-gray-200 dark:border-gray-700
                            flex items-center justify-between px-4">

                    <div class="flex items-center gap-3 min-w-0">
                        <a href="{{ route('owner.chats.index') }}" class="lg:hidden w-9 h-9 rounded-full flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                        </a>
                        <div class="relative flex-shrink-0">
                            @if($userAvatar)
                                <img src="{{ asset('storage/' . $userAvatar) }}" alt="{{ $userName }}" class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-base shadow">
                                    {{ strtoupper(substr($userName, 0, 1)) }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800 {{ $isOnline ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        </div>
                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900 dark:text-white truncate text-sm md:text-base">{{ $userName }}</h2>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="{{ $isOnline ? 'text-green-500' : 'text-gray-500 dark:text-gray-400' }}">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <span class="text-gray-500 dark:text-gray-400 truncate">{{ $userRole }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button onclick="toggleChatSearch()" class="w-9 h-9 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Cari pesan">
                            <i class="fas fa-search"></i>
                        </button>
                        <button onclick="toggleUserInfo()" class="w-9 h-9 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Info pengguna">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>

                {{-- SEARCH BAR --}}
                <div id="chatSearchBar" class="hidden px-4 py-2 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="messageSearch" placeholder="Cari pesan..." class="w-full pl-9 pr-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>
                </div>

                {{-- MESSAGE AREA --}}
                <div id="messageContainer" class="flex-1 overflow-y-auto px-4 md:px-6 py-4 bg-[#efeae2] dark:bg-gray-900 relative">
                    <div class="absolute inset-0 opacity-[0.035] pointer-events-none" style="background-image: radial-gradient(circle at 20px 20px, #64748b 1px, transparent 1px); background-size: 40px 40px;"></div>

                    <div id="messagesWrapper" class="relative space-y-2 max-w-4xl mx-auto">
                        @if($messages->count())
                            @foreach($messages as $msg)
                                @php $isMine = $msg->sender_id == auth()->id(); @endphp
                                <div class="message-item flex {{ $isMine ? 'justify-end' : 'justify-start' }}" data-message="{{ strtolower($msg->message) }}">
                                    <div class="max-w-[82%] sm:max-w-[70%]">
                                        <div class="px-4 py-2.5 rounded-2xl shadow-sm {{ $isMine ? 'bg-cyan-600 text-white rounded-br-md' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-bl-md' }}">
                                            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                                            <div class="mt-1 flex items-center justify-end gap-1">
                                                <span class="text-[10px] {{ $isMine ? 'text-cyan-100' : 'text-gray-400 dark:text-gray-500' }}">{{ $msg->created_at->format('H:i') }}</span>
                                                @if($isMine)
                                                    <i class="fas fa-check-double text-[10px] {{ $msg->status == 'read' ? 'text-blue-300' : 'text-cyan-100' }}"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex justify-center py-8">
                                <div class="text-center text-gray-400 text-sm">
                                    <i class="fas fa-comment text-3xl block mb-2 opacity-50"></i>
                                    <p>Belum ada pesan. Mulai percakapan!</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- COMPOSER --}}
                <div class="flex-shrink-0 px-3 md:px-5 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <form id="chatForm" class="flex items-end gap-2">
                        <input type="hidden" name="conversation_id" value="{{ $conversation->id ?? '' }}">
                        <button type="button" onclick="document.getElementById('chatAttachment').click()" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" id="chatAttachment" class="hidden">

                        <div class="flex-1 relative">
                            <textarea id="chatInput" name="message" rows="1" placeholder="Tulis pesan..." autocomplete="off" class="w-full resize-none max-h-32 rounded-2xl bg-gray-100 dark:bg-gray-700 border-none px-4 py-2.5 pr-12 text-sm text-gray-800 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-500 focus:outline-none"></textarea>
                            <button type="button" onclick="insertEmoji()" class="absolute right-3 bottom-2 w-7 h-7 flex items-center justify-center text-gray-400 hover:text-yellow-500 transition">
                                <i class="far fa-smile text-lg"></i>
                            </button>
                        </div>

                        <button type="submit" id="sendButton" class="flex-shrink-0 w-10 h-10 rounded-full bg-cyan-600 hover:bg-cyan-700 active:scale-95 text-white flex items-center justify-center shadow-md transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    <div id="attachmentPreview" class="hidden mt-2 ml-12 text-xs text-gray-500 dark:text-gray-400"></div>
                </div>

            @else

                {{-- Empty state --}}
                <div class="flex-1 flex items-center justify-center bg-[#efeae2] dark:bg-gray-900">
                    <div class="text-center text-gray-400 dark:text-gray-500 p-8">
                        <i class="fas fa-comments text-6xl block mb-4 opacity-30"></i>
                        <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-300">Pilih percakapan</h3>
                        <p class="text-sm mt-1">Pilih kontak dari daftar di sebelah kiri untuk memulai chat.</p>
                        <button onclick="openNewChat()" class="mt-4 px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition text-sm">
                            <i class="fas fa-plus mr-1"></i> Chat Baru
                        </button>
                    </div>
                </div>

            @endif
        </div>

        {{-- ========================================================= --}}
        {{-- PANEL INFO USER --}}
        {{-- ========================================================= --}}
        @if($chatUser)
        <aside id="userInfoPanel" class="hidden xl:flex w-[300px] flex-shrink-0 border-l border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex-col">
            <div class="h-[72px] px-5 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Informasi</h3>
                <button onclick="toggleUserInfo()" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                @if($userAvatar)
                    <img src="{{ asset('storage/' . $userAvatar) }}" alt="{{ $userName }}" class="w-20 h-20 mx-auto rounded-full object-cover shadow-lg">
                @else
                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                @endif
                <h3 class="mt-3 text-lg font-bold text-gray-900 dark:text-white">{{ $userName }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $userRole }}</p>
                @if($isOnline)
                    <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs font-medium">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span> Online
                    </div>
                @endif
            </div>
            <div class="p-5 space-y-4 overflow-y-auto">
                <div><p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">NIM</p><p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $userNim }}</p></div>
                <div><p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Telepon</p><p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $userPhone }}</p></div>
                <div><p class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-500">Role</p><p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $userRole }}</p></div>
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-600 dark:text-gray-300">
                    <i class="fas fa-lock text-green-500 mr-1"></i> Percakapan privat terenkripsi.
                </div>
            </div>
        </aside>
        @endif
    </div>
</div>

{{-- ================================================================ --}}
{{-- JAVASCRIPT CHAT --}}
{{-- ================================================================ --}}
<script>
    let pollInterval = null;
    let lastMessageId = {{ $messages->last()->id ?? 0 }};
    const conversationId = {{ $conversation->id ?? 0 }};

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messageContainer');
        const input = document.getElementById('chatInput');
        const form = document.getElementById('chatForm');

        // Scroll ke bawah
        if (container) container.scrollTop = container.scrollHeight;

        // Auto-resize textarea
        if (input) {
            input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 128) + 'px';
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                }
            });
        }

        // Submit form
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = input.value.trim();
                if (!message) return;
                sendMessage(message);
            });
        }

        // Start polling
        if (conversationId > 0) {
            startPolling();
        }

        // Search messages
        document.getElementById('messageSearch')?.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();
            document.querySelectorAll('.message-item').forEach(el => {
                const text = el.dataset.message || '';
                el.classList.toggle('hidden', keyword && !text.includes(keyword));
            });
        });
    });

   function sendMessage(message) {
    // CEK PENTING: Pastikan ID percakapan ada
    if (conversationId === 0) {
        alert('Error: ID percakapan tidak ditemukan. Silakan refresh halaman.');
        return;
    }

    const btn = document.getElementById('sendButton');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("owner.chats.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: message
        })
    })
    .then(async res => {
        // CEK: Jika response bukan 200 OK, lempar error dengan status HTTP
        if (!res.ok) {
            // Ambil teks error (bisa jadi HTML)
            const errorText = await res.text();
            throw new Error(`Server mengembalikan status ${res.status}. Detail: ${errorText.substring(0, 100)}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            appendMessage(data.message, true);
            document.getElementById('chatInput').value = '';
            document.getElementById('chatInput').style.height = 'auto';
            lastMessageId = data.message.id;
        } else {
            alert(data.message || 'Gagal mengirim pesan.');
        }
    })
    .catch(err => {
        // Menampilkan error yang sebenarnya
        alert('Gagal terkirim: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    });
}

    function appendMessage(msg, isMine = true) {
        const wrapper = document.getElementById('messagesWrapper');
        const container = document.getElementById('messageContainer');
        const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        const div = document.createElement('div');
        div.className = `message-item flex ${isMine ? 'justify-end' : 'justify-start'}`;
        div.dataset.message = msg.message.toLowerCase();

        div.innerHTML = `
            <div class="max-w-[82%] sm:max-w-[70%]">
                <div class="px-4 py-2.5 rounded-2xl shadow-sm ${isMine ? 'bg-cyan-600 text-white rounded-br-md' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-bl-md'}">
                    <p class="text-sm leading-relaxed whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
                    <div class="mt-1 flex items-center justify-end gap-1">
                        <span class="text-[10px] ${isMine ? 'text-cyan-100' : 'text-gray-400 dark:text-gray-500'}">${time}</span>
                        ${isMine ? '<i class="fas fa-check-double text-[10px] text-cyan-100"></i>' : ''}
                    </div>
                </div>
            </div>
        `;

        wrapper.appendChild(div);
        setTimeout(() => container.scrollTop = container.scrollHeight, 50);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            fetch('{{ route("owner.chats.poll") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    last_id: lastMessageId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.messages.length) {
                    data.messages.forEach(msg => {
                        const isMine = msg.sender_id == {{ auth()->id() }};
                        appendMessage(msg, isMine);
                        if (msg.id > lastMessageId) lastMessageId = msg.id;
                    });
                }
            })
            .catch(() => {});
        }, 3000);
    }

    function toggleChatSearch() {
        document.getElementById('chatSearchBar').classList.toggle('hidden');
        if (!document.getElementById('chatSearchBar').classList.contains('hidden')) {
            document.getElementById('messageSearch')?.focus();
        }
    }

    function toggleUserInfo() {
        const panel = document.getElementById('userInfoPanel');
        if (panel) {
            panel.classList.toggle('hidden');
            panel.classList.toggle('xl:flex');
        }
    }

    function insertEmoji() {
        const input = document.getElementById('chatInput');
        if (input) {
            input.value += '😊';
            input.focus();
        }
    }

    function openNewChat() {
        // Implementasi pencarian user untuk chat baru
        const name = prompt('Masukkan nama atau NIM pengguna:');
        if (name) {
            fetch('{{ route("owner.chats.search") }}?q=' + encodeURIComponent(name))
                .then(res => res.json())
                .then(users => {
                    if (users.length === 0) {
                        alert('Pengguna tidak ditemukan.');
                    } else if (users.length === 1) {
                        window.location.href = '{{ route("owner.chats.private", "") }}/' + users[0].user_uuid;
                    } else {
                        // Tampilkan pilihan
                        let msg = 'Pilih pengguna:\n';
                        users.forEach((u, i) => msg += `${i+1}. ${u.name} (${u.nim || '-'})\n`);
                        const choice = prompt(msg + '\nMasukkan nomor:');
                        if (choice && users[choice-1]) {
                            window.location.href = '{{ route("owner.chats.private", "") }}/' + users[choice-1].user_uuid;
                        }
                    }
                })
                .catch(() => alert('Error mencari pengguna.'));
        }
    }

    // Bersihkan polling saat keluar
    window.addEventListener('beforeunload', function() {
        if (pollInterval) clearInterval(pollInterval);
    });
</script>

<style>
    .message-item.hidden { display: none; }
</style>

@endsection