@extends('layouts.roles.' . auth()->user()->getRoleLayout())

@section('title', 'Detail Notifikasi – UNICODE')
@section('page-title', 'Detail Notifikasi')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('owner.notifications.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar notifikasi
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-lg">

        {{-- Header --}}
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $notification->title }}</h1>
                    @if($notification->is_starred)
                        <span class="text-yellow-500 text-lg"><i class="fas fa-star"></i></span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="px-2 py-1 rounded-full text-xs bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        {{ ucfirst(str_replace('_', ' ', $notification->type ?? 'Sistem')) }}
                    </span>
                    <span class="px-2 py-1 rounded-full text-xs {{ $notification->priority_badge }}">
                        {{ ucfirst($notification->priority) }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="far fa-clock mr-1"></i> {{ $notification->created_at->format('d F Y H:i') }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                    @if($notification->is_expired)
                        <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Kadaluarsa</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                @if(is_null($notification->read_at))
                    <form action="{{ route('owner.notifications.read', $notification->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition shadow-md shadow-blue-500/20">
                            <i class="fas fa-check mr-2"></i> Tandai dibaca
                        </button>
                    </form>
                @else
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-lg inline-flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Sudah dibaca
                    </span>
                @endif
                <form action="{{ route('owner.notifications.toggle-star', $notification->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ $notification->is_starred ? 'text-yellow-500 border-yellow-500' : 'text-gray-500' }}">
                        <i class="fas {{ $notification->is_starred ? 'fa-star' : 'fa-star' }}"></i> {{ $notification->is_starred ? 'Disimpan' : 'Simpan' }}
                    </button>
                </form>
                @if(!$notification->is_archived)
                    <form action="{{ route('owner.notifications.archive', $notification->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition">
                            <i class="fas fa-archive mr-2"></i> Arsipkan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6">

            {{-- Pesan --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pesan</h3>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap border border-gray-200 dark:border-gray-700">
                    {{ $notification->message }}
                </div>
            </div>

            {{-- Sumber / Referensi --}}
            @if($notification->reference_type && $notification->reference_id)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Sumber Aktivitas</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ ucfirst(str_replace('_', ' ', $notification->reference_type)) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID Referensi: #{{ $notification->reference_id }}</p>
                        @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                                <i class="fas fa-external-link-alt mr-1"></i> Buka halaman terkait
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Pengirim --}}
            @if($notification->sender)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Pengirim</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 flex flex-col sm:flex-row items-start gap-4 border border-gray-200 dark:border-gray-700">
                        <img src="{{ $notification->sender->avatar_url ? asset('storage/'.$notification->sender->avatar_url) : asset('images/default-avatar.png') }}"
                             alt="{{ $notification->sender->name }}"
                             class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600 flex-shrink-0">
                        <div class="flex-1 w-full">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $notification->sender->name }}</p>
                                @if($notification->sender_id == auth()->id())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="fas fa-user-check text-xs"></i> Dikirim oleh Anda
                                    </span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div><span class="text-gray-500 dark:text-gray-400">NIM</span> <p class="text-gray-800 dark:text-white">{{ $notification->sender->nim ?? '-' }}</p></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Email</span> <p class="text-gray-800 dark:text-white">{{ $notification->sender->email }}</p></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Telepon</span> <p class="text-gray-800 dark:text-white">{{ $notification->sender->phone ?? '-' }}</p></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Level</span> <p class="text-gray-800 dark:text-white">{{ $notification->sender->level->name ?? '-' }}</p></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Status</span> <p class="text-gray-800 dark:text-white">{{ $notification->sender->is_active ? 'Aktif' : 'Tidak Aktif' }}</p></div>
                                <div><span class="text-gray-500 dark:text-gray-400">Bergabung</span> <p class="text-gray-800 dark:text-white">{{ $notification->sender->created_at->format('d F Y') }}</p></div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($notification->sender_id != auth()->id())
                                    <a href="{{ route('owner.chats.private', $notification->sender->user_uuid) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm shadow-md shadow-green-500/20">
                                        <i class="fas fa-comment-dots mr-2"></i> Balas via Chat
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-lg text-sm cursor-not-allowed">
                                        <i class="fas fa-user mr-2"></i> Anda sendiri
                                    </span>
                                @endif
                                <a href="{{ route('profile.public', $notification->sender->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                                    <i class="fas fa-user mr-2"></i> Lihat Profil
                                </a>
                                @if($notification->sender_id == auth()->id())
                                    <a href="{{ route('owner.notifications.index', ['sender' => $notification->sender_id]) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-sm">
                                        <i class="fas fa-list mr-2"></i> Notifikasi saya
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Aksi berdasarkan tipe --}}
            @php
                $actionButtons = [];
                if ($notification->type === 'incoming_mail' && $notification->reference_type === 'incoming_mail') {
                    $actionButtons[] = ['label' => 'Lihat Surat', 'url' => route('owner.officials.secretary.incoming-mails.show', $notification->reference_id), 'icon' => 'fa-eye'];
                    $actionButtons[] = ['label' => 'Setujui', 'url' => '#', 'icon' => 'fa-check', 'class' => 'bg-green-600 hover:bg-green-700'];
                    $actionButtons[] = ['label' => 'Tolak', 'url' => '#', 'icon' => 'fa-times', 'class' => 'bg-red-600 hover:bg-red-700'];
                } elseif ($notification->type === 'chat') {
                    $actionButtons[] = ['label' => 'Buka Chat', 'url' => route('owner.chats.private', $notification->sender->user_uuid ?? ''), 'icon' => 'fa-comments'];
                } elseif ($notification->type === 'task') {
                    $actionButtons[] = ['label' => 'Lihat Tugas', 'url' => '#', 'icon' => 'fa-tasks'];
                    $actionButtons[] = ['label' => 'Tandai Dikerjakan', 'url' => '#', 'icon' => 'fa-check-double'];
                } elseif ($notification->type === 'agenda') {
                    $actionButtons[] = ['label' => 'Lihat Agenda', 'url' => '#', 'icon' => 'fa-calendar-alt'];
                    $actionButtons[] = ['label' => 'Tambahkan ke Jadwal', 'url' => '#', 'icon' => 'fa-plus-circle'];
                } else {
                    $actionButtons[] = ['label' => 'Lihat Detail', 'url' => $notification->action_url ?? $notification->link ?? '#', 'icon' => 'fa-arrow-right'];
                }
            @endphp

            @if(count($actionButtons) > 0)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tindakan</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($actionButtons as $btn)
                            <a href="{{ $btn['url'] }}" class="inline-flex items-center px-4 py-2 {{ $btn['class'] ?? 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg transition text-sm shadow-md">
                                <i class="fas {{ $btn['icon'] }} mr-2"></i> {{ $btn['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Lampiran (jika ada) --}}
            @if($notification->data && isset($notification->data['attachment']))
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Lampiran</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $notification->data['attachment']['name'] ?? 'file' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->data['attachment']['size'] ?? '2.4 MB' }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="#" class="text-blue-600 hover:underline text-sm"><i class="fas fa-eye mr-1"></i> Lihat</a>
                            <a href="#" class="text-green-600 hover:underline text-sm"><i class="fas fa-download mr-1"></i> Download</a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Percakapan Terkait (Mini Chat) --}}
            @if($conversation && $messages->count() > 0)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Percakapan Terkait</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700 space-y-2 max-h-64 overflow-y-auto">
                        @foreach($messages as $msg)
                            <div class="flex items-start gap-2 {{ $msg->sender_id == auth()->id() ? 'justify-end' : '' }}">
                                @if($msg->sender_id != auth()->id())
                                    <img src="{{ $msg->sender->avatar_url ? asset('storage/'.$msg->sender->avatar_url) : asset('images/default-avatar.png') }}" class="w-7 h-7 rounded-full object-cover">
                                @endif
                                <div class="max-w-[70%] {{ $msg->sender_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} rounded-lg px-3 py-2 text-sm">
                                    <p>{{ $msg->message }}</p>
                                    <span class="text-[10px] {{ $msg->sender_id == auth()->id() ? 'text-blue-200' : 'text-gray-500' }}">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                                @if($msg->sender_id == auth()->id())
                                    <img src="{{ auth()->user()->avatar_url ? asset('storage/'.auth()->user()->avatar_url) : asset('images/default-avatar.png') }}" class="w-7 h-7 rounded-full object-cover">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    {{-- Form balas cepat --}}
                    <div class="mt-2 flex gap-2">
                        <input type="text" placeholder="Tulis balasan..." class="flex-1 px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 text-sm" id="quick_reply">
                        <button onclick="sendQuickReply({{ $conversation->id }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Notifikasi Terkait --}}
            @if($relatedNotifications->count() > 0)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Notifikasi Terkait</h3>
                    <div class="space-y-2">
                        @foreach($relatedNotifications as $rel)
                            <a href="{{ route('owner.notifications.show', $rel->id) }}" class="block p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <div class="flex items-center gap-3">
                                    <i class="{{ $rel->icon }} text-sm text-gray-500"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $rel->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $rel->created_at->diffForHumans() }}</p>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Riwayat Aktivitas (dari data) --}}
            @if($notification->data && isset($notification->data['history']))
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Riwayat Aktivitas</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700 space-y-1 text-sm">
                        @foreach($notification->data['history'] as $item)
                            <div class="flex items-start gap-2">
                                <span class="text-xs text-gray-400 w-24 flex-shrink-0">{{ $item['time'] ?? '' }}</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $item['description'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Data Tambahan --}}
            @if($notification->data && !isset($notification->data['attachment']) && !isset($notification->data['history']))
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Data Tambahan</h3>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 overflow-x-auto">
                        <pre class="whitespace-pre-wrap">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-between flex-wrap gap-2">
            <div class="flex gap-2">
                @if($notification->is_archived)
                    <form action="{{ route('owner.notifications.unarchive', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition text-sm">
                            <i class="fas fa-undo mr-2"></i> Kembalikan dari Arsip
                        </button>
                    </form>
                @endif
            </div>
            <form action="{{ route('owner.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm shadow-md shadow-red-500/20">
                    <i class="fas fa-trash mr-2"></i> Hapus Permanen
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function sendQuickReply(conversationId) {
        const input = document.getElementById('quick_reply');
        const message = input.value.trim();
        if (!message) return;

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
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                location.reload();
            } else {
                alert('Gagal mengirim pesan.');
            }
        })
        .catch(() => alert('Terjadi kesalahan.'));
    }
</script>
@endpush
@endsection