{{-- resources/views/ukm/pemrograman.blade.php --}}
@extends('layouts.app')

@section('title', 'UKM Pemrograman | UNICODE')
@section('meta_description', 'Kenali UKM Pemrograman UNICODE: profil, nilai inti, visi-misi, program kerja strategis, struktur, portofolio, dan peluang kolaborasi.')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    /* =========================================================
     | Helper — Route aman (anti RouteNotFound)
     * ========================================================= */
    $safeRoute = static function (string $name, string $fallback = '#'): string {
        return Route::has($name) ? route($name) : $fallback;
    };

    $landingUrl = $safeRoute('landing', url('/'));
    $ukmUrl     = Route::has('ukm.index') ? route('ukm.index') : url('/ukm');

    /* =========================================================
     | DATA KONTEN
     * ========================================================= */
    $sections = [
        ['id' => 'tentang', 'label' => 'Tentang'],
        ['id' => 'nilai', 'label' => 'Nilai Inti'],
        ['id' => 'visi-misi', 'label' => 'Visi & Misi'],
        ['id' => 'program', 'label' => 'Program Unggulan'],
        ['id' => 'struktur', 'label' => 'Struktur'],
        ['id' => 'tech-stack', 'label' => 'Tech Stack'],
        ['id' => 'pengurus', 'label' => 'Pengurus'],
    ];

    $coreValues = [
        ['icon' => 'fas fa-handshake', 'title' => 'Kolaborasi', 'text' => 'Membangun sesuatu bersama dan saling memberi umpan balik.', 'color' => '#06b6d4'],
        ['icon' => 'fas fa-lightbulb', 'title' => 'Inovasi', 'text' => 'Mengubah rasa ingin tahu menjadi pertanyaan dan solusi.', 'color' => '#8b5cf6'],
        ['icon' => 'fas fa-seedling', 'title' => 'Pertumbuhan', 'text' => 'Menghargai proses belajar, eksperimen, dan perbaikan.', 'color' => '#10b981'],
        ['icon' => 'fas fa-shield-heart', 'title' => 'Tanggung Jawab', 'text' => 'Menjaga etika, integritas, dan saling menghormati.', 'color' => '#f59e0b'],
    ];

    $missions = [
        'Menyelenggarakan workshop dan pelatihan teknologi terkini secara berkelanjutan.',
        'Membina tim competitive programming untuk berkompetisi di tingkat nasional.',
        'Membangun produk digital nyata yang relevan dengan kebutuhan kampus dan masyarakat.',
        'Berpartisipasi aktif di hackathon dan pameran karya inovasi digital.',
    ];

    $mainPrograms = [
        [
            'icon' => 'fas fa-globe', 
            'title' => 'Ekosistem Website UNICODE', 
            'desc' => 'Membiasakan anggota dengan ekosistem website terintegrasi: absensi, game, misi, kemudahan informasi ruangan, jadwal, dan event.', 
            'color' => '#06b6d4',
            'tags' => ['Absensi QR/NFC', 'Game Center', 'Manajemen Ruang']
        ],
        [
            'icon' => 'fas fa-code-branch', 
            'title' => 'Build Project', 
            'desc' => 'Menumbuhkan inovasi dengan belajar pemrograman secara langsung dalam membuat project nyata dari nol hingga rilis.', 
            'color' => '#3b82f6',
            'tags' => ['Web Development', 'Mobile App', 'Open Source']
        ],
        [
            'icon' => 'fas fa-network-wired', 
            'title' => 'UNICODE Tech Connect', 
            'desc' => 'Memperluas jejaring melalui kolaborasi antar komunitas & organisasi, kompetisi, dan pameran karya digital.', 
            'color' => '#8b5cf6',
            'tags' => ['Hackathon', 'Tech Talk', 'Pameran Karya']
        ],
    ];

    // Struktur Organisasi sesuai PDF Bagan
    $structure = [
        'core' => [
            ['title' => 'Ketua', 'sub' => 'UKM Pemrograman UNICODE', 'icon' => 'fas fa-crown'],
            ['title' => 'Wakil Ketua', 'sub' => 'UKM Pemrograman UNICODE', 'icon' => 'fas fa-user-tie'],
            ['title' => 'Sekretaris & Bendahara', 'sub' => 'Pengurus Inti / BPH', 'icon' => 'fas fa-users-cog'],
        ],
        'divisions' => [
            [
                'title' => 'PJ Divisi Pengembang',
                'sub' => 'Project / Development',
                'asisten' => 'Asisten PJ Pengembang',
                'anggota' => 'Anggota Divisi Pengembang',
                'icon' => 'fas fa-code',
                'color' => '#06b6d4'
            ],
            [
                'title' => 'PJ Divisi PDD',
                'sub' => 'Publikasi, Dokumentasi & Desain',
                'asisten' => 'Asisten PJ Divisi PDD',
                'anggota' => 'Anggota Divisi PDD',
                'icon' => 'fas fa-bullhorn',
                'color' => '#8b5cf6'
            ]
        ]
    ];

    $techStack = [
        'Laravel', 'React', 'Vue', 'Node.js', 'Python', 'PHP', 
        'TailwindCSS', 'MySQL', 'PostgreSQL', 'Docker', 'Git', 
        'AI/ML', 'TensorFlow', 'Flutter', 'TypeScript', 'Express'
    ];

    $members = [
        ['name' => 'Fikri Ramadhan', 'role' => 'Ketua', 'nim' => '2023020010'],
        ['name' => 'Salsabila', 'role' => 'Wakil Ketua', 'nim' => '2023020011'],
        ['name' => 'Kevin Tanaya', 'role' => 'Sekretaris', 'nim' => '2023020012'],
        ['name' => 'Dewi Lestari', 'role' => 'Bendahara', 'nim' => '2023020013'],
    ];
@endphp

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    
    /* Grid Pattern */
    .bg-grid-pattern {
        background-image:
            linear-gradient(to right, rgba(0, 0, 0, 0.04) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(0, 0, 0, 0.04) 1px, transparent 1px);
        background-size: 32px 32px;
    }
    .dark .bg-grid-pattern {
        background-image:
            linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
    }

    #ukm-pemrograman-page section { scroll-margin-top: 6rem; }
    #ukm-pemrograman-page .nav-scroll::-webkit-scrollbar { display: none; }
    #ukm-pemrograman-page .nav-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    
    .connector-line { background-color: rgba(148, 163, 184, 0.4); }
    .dark .connector-line { background-color: rgba(255, 255, 255, 0.15); }

    /* Kursor Animasi Mengetik */
    .typewriter-cursor {
        display: inline-block;
        width: 4px;
        height: 0.8em;
        background-color: #06b6d4;
        margin-left: 6px;
        animation: blink 1s step-end infinite;
        vertical-align: baseline;
    }
    @keyframes blink { 50% { opacity: 0; } }

    /* Reveal Animation */
    .ukm-reveal { opacity: 0; transform: translateY(20px); transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1); }
    .ukm-reveal.is-visible { opacity: 1; transform: translateY(0); }
</style>
@endpush

<main id="ukm-pemrograman-page" class="min-h-screen overflow-hidden bg-slate-50 dark:bg-[#060B19] text-slate-800 dark:text-slate-200 selection:bg-cyan-500/30 selection:text-cyan-900 dark:selection:text-cyan-200 font-sans transition-colors duration-300">

    {{-- ═══════════════════ HERO SECTION ═══════════════════ --}}
    <section class="relative isolate overflow-hidden border-b border-slate-200 dark:border-white/5">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-32 -top-32 h-[40rem] w-[40rem] rounded-full bg-cyan-300/30 dark:bg-cyan-600/10 blur-[120px] transition-colors duration-500"></div>
            <div class="absolute right-0 top-0 h-[40rem] w-[40rem] rounded-full bg-blue-300/30 dark:bg-blue-600/10 blur-[120px] transition-colors duration-500"></div>
            <div class="absolute inset-0 bg-grid-pattern opacity-70 dark:opacity-50"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-50 dark:to-[#060B19]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-10 sm:px-6 sm:pb-28 sm:pt-16 lg:px-8">
            <nav aria-label="Breadcrumb" class="mb-10 text-xs font-medium tracking-wide">
                <ol class="flex flex-wrap items-center gap-2 text-slate-500 dark:text-slate-400">
                    <li><a href="{{ $landingUrl }}" class="transition-colors hover:text-[#06b6d4]">Beranda</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[10px] opacity-50"></i></li>
                    <li><a href="{{ $ukmUrl }}" class="transition-colors hover:text-[#06b6d4]">UKM</a></li>
                    <li aria-hidden="true"><i class="fas fa-chevron-right text-[10px] opacity-50"></i></li>
                    <li class="font-bold text-[#06b6d4]" aria-current="page">Pemrograman</li>
                </ol>
            </nav>

            <div class="grid items-center gap-12 lg:grid-cols-[1fr_420px] lg:gap-16">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2.5 rounded-full border border-[#06b6d4]/30 bg-[#06b6d4]/10 px-4 py-2 text-[11px] font-bold uppercase tracking-widest text-[#0891b2] dark:text-[#22d3ee] backdrop-blur-md shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#06b6d4] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#06b6d4]"></span>
                        </span>
                        Unit Kegiatan Mahasiswa
                    </div>

                    {{-- Title with Typewriter Animation --}}
                    <h1 class="mt-6 text-5xl font-black leading-[1.1] tracking-tight text-slate-900 dark:text-white sm:text-6xl lg:text-7xl">
                        <div class="flex items-center min-h-[1.2em]">
                            <span id="typewriter-ukm"></span>
                            <span class="typewriter-cursor" id="cursor-ukm"></span>
                        </div>
                        <div class="flex items-center mt-1 pb-2 min-h-[1.2em]">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#06b6d4] via-[#3b82f6] to-[#8b5cf6] dark:from-[#22d3ee] dark:via-[#60a5fa] dark:to-[#a78bfa]" id="typewriter-pemrograman"></span>
                            <span class="typewriter-cursor hidden" id="cursor-pemrograman"></span>
                        </div>
                    </h1>

                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-slate-600 dark:text-slate-400 sm:text-lg sm:leading-8">
                        Programming & Developer Club — Universitas Bhakti Asih. Wadah mahasiswa untuk belajar, membangun proyek nyata, dan berkolaborasi dalam ekosistem teknologi digital.
                    </p>

                    <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:flex-wrap">
                        <a href="#program" class="group inline-flex min-h-[3.5rem] items-center justify-center gap-2 rounded-xl bg-[#06b6d4] hover:bg-[#0891b2] px-8 py-3 text-sm font-black text-white shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all hover:-translate-y-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#06b6d4] focus-visible:ring-offset-2">
                            <i class="fas fa-rocket transition-transform group-hover:-translate-y-1 group-hover:translate-x-1" aria-hidden="true"></i>
                            Lihat Program
                        </a>
                        <a href="#pengurus" class="inline-flex min-h-[3.5rem] items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white dark:border-white/20 dark:bg-white/5 px-8 py-3 text-sm font-bold text-slate-700 dark:text-white shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#06b6d4]">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            Pengurus
                        </a>
                    </div>
                </div>

                {{-- Card Futuristik (Logo & Stats) --}}
                <div class="relative mx-auto w-full max-w-sm lg:mx-0 lg:max-w-none">
                    <div aria-hidden="true" class="absolute -inset-0.5 rounded-[2.5rem] bg-gradient-to-br from-[#06b6d4] to-[#8b5cf6] opacity-30 dark:opacity-40 blur-xl transition-opacity"></div>
                    <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white/90 dark:border-white/10 dark:bg-[#0A1128]/90 p-1 shadow-xl backdrop-blur-2xl">
                        <div class="rounded-[1.85rem] bg-gradient-to-b from-slate-50 to-white dark:from-white/[0.05] dark:to-transparent p-6 sm:p-8 flex flex-col items-center text-center">
                            
                            <div class="relative mb-6">
                                <div class="absolute inset-0 rounded-full bg-[#06b6d4] opacity-20 blur-xl"></div>
                                <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo UNICODE" class="relative h-28 w-28 rounded-full border-4 border-white dark:border-[#0A1128] object-cover shadow-lg">
                            </div>

                            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">UNICODE</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Code. Create. Innovate.</p>

                            <div class="grid grid-cols-3 gap-3 w-full">
                                @foreach(['Anggota' => 78, 'Proker' => 20, 'Prestasi' => 14] as $lbl => $v)
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 dark:border-white/5 dark:bg-[#060B19]/50 p-3">
                                        <p class="text-lg font-black text-[#06b6d4]">{{ $v }}</p>
                                        <p class="text-[9px] uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ $lbl }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════ STICKY NAVIGATION ═══════════════════ --}}
    <div class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 dark:border-white/5 dark:bg-[#060B19]/90 backdrop-blur-xl transition-colors duration-300">
        <nav aria-label="Navigasi halaman" class="nav-scroll mx-auto max-w-7xl overflow-x-auto px-4 sm:px-6 lg:px-8">
            <ul class="flex min-w-max gap-2 py-3">
                @foreach($sections as $section)
                    <li>
                        <a href="#{{ $section['id'] }}" class="inline-flex rounded-lg px-4 py-2 text-xs font-bold text-slate-500 transition-all hover:bg-slate-100 hover:text-[#06b6d4] dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-[#22d3ee] focus:outline-none sm:text-sm">
                            {{ $section['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8 space-y-32">

        {{-- ═══════════════════ 01. TENTANG ═══════════════════ --}}
        <section id="tentang" class="relative ukm-reveal">
            <div class="grid gap-12 lg:grid-cols-2 lg:gap-20 items-center">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#06b6d4]/10 text-[#06b6d4] ring-1 ring-[#06b6d4]/30 shadow-sm"><i class="fas fa-info-circle text-lg"></i></span>
                        <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Rumah bagi<br>Inovator Digital.</h2>
                    </div>
                    <div class="space-y-6 text-base leading-relaxed text-slate-600 dark:text-slate-400">
                        <p>UKM Pemrograman UNICODE adalah rumah bagi mahasiswa yang ingin menekuni dunia teknologi — dari web development, mobile, AI, hingga competitive programming. Kami membangun ekosistem belajar yang kolaboratif dan berorientasi proyek nyata.</p>
                        <p>Fokus kami bukan hanya sekadar menulis kode, tetapi bagaimana memecahkan masalah, berkolaborasi dalam tim, dan menghasilkan karya digital yang berdampak.</p>
                        
                        <div class="relative rounded-2xl border border-slate-200 bg-white p-5 mt-8 overflow-hidden group shadow-sm dark:border-white/10 dark:bg-[#0A1128] dark:shadow-none">
                            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-[#06b6d4] to-[#3b82f6]"></div>
                            <div class="absolute -right-4 -bottom-4 text-6xl text-slate-100 dark:text-white/5 group-hover:text-slate-200 dark:group-hover:text-white/10 transition-colors"><i class="fas fa-quote-right"></i></div>
                            <p class="relative z-10 text-sm italic text-slate-700 dark:text-slate-300">"Tidak harus hebat untuk memulai. Tetapi harus memulai untuk menjadi hebat."</p>
                        </div>
                    </div>
                </div>

                {{-- Visual: Code Editor Mockup --}}
                <div class="relative">
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-br from-[#06b6d4] to-[#8b5cf6] opacity-20 blur-lg"></div>
                    <div class="relative rounded-2xl border border-slate-200 bg-[#0d1117] shadow-2xl overflow-hidden dark:border-white/10">
                        <div class="flex items-center justify-between border-b border-slate-700/50 bg-[#161b22] px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-red-500/80"></div>
                                <div class="h-3 w-3 rounded-full bg-yellow-500/80"></div>
                                <div class="h-3 w-3 rounded-full bg-green-500/80"></div>
                                <span class="ml-4 text-xs font-mono text-slate-400">unicode.php</span>
                            </div>
                            <i class="fas fa-code text-slate-500 text-sm"></i>
                        </div>
                        <div class="p-6 font-mono text-sm leading-loose overflow-x-auto text-slate-300">
                            <pre><code><span class="text-pink-400">class</span> <span class="text-yellow-300">Unicode</span> <span class="text-cyan-300">{</span>
    <span class="text-pink-400">public function</span> <span class="text-blue-300">__construct</span>() <span class="text-cyan-300">{</span>
        <span class="text-slate-500">// Core Values</span>
        <span class="text-purple-400">$this</span>->values = [
            <span class="text-green-300">'Integritas'</span>,
            <span class="text-green-300">'Inklusif'</span>,
            <span class="text-green-300">'Eksploratif'</span>,
            <span class="text-green-300">'Kolaboratif'</span>
        ];
    <span class="text-cyan-300">}</span>

    <span class="text-pink-400">public function</span> <span class="text-blue-300">buildFuture</span>() <span class="text-cyan-300">{</span>
        <span class="text-pink-400">return</span> <span class="text-purple-400">$this</span>->belajar()
                    -><span class="text-blue-300">berkolaborasi</span>()
                    -><span class="text-blue-300">berkarya</span>();
    <span class="text-cyan-300">}</span>
<span class="text-cyan-300">}</span></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════ 02. NILAI INTI ═══════════════════ --}}
        <section id="nilai" class="pt-16 border-t border-slate-200 dark:border-white/5 ukm-reveal">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Nilai Inti Organisasi</h2>
                <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Empat pilar utama yang menjadi fondasi gerak langkah UKM Pemrograman.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($coreValues as $val)
                    <div class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg dark:border-white/10 dark:bg-[#0A1128] dark:hover:shadow-[0_15px_40px_rgba(0,0,0,0.4)]">
                        <div class="absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-10" style="background: linear-gradient(to bottom right, {{ $val['color'] }}, transparent);"></div>
                        <div class="relative z-10">
                            <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm transition-transform group-hover:scale-110" 
                                 style="background-color: {{ $val['color'] }}15; color: {{ $val['color'] }};">
                                <i class="{{ $val['icon'] }} text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ $val['title'] }}</h3>
                            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $val['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ═══════════════════ 03. VISI & MISI ═══════════════════ --}}
        <section id="visi-misi" class="pt-16 border-t border-slate-200 dark:border-white/5 ukm-reveal">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Visi & Misi Organisasi</h2>
                <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Arah dan langkah strategis UKM Pemrograman untuk menciptakan ekosistem teknologi yang berdampak.</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-[1fr_1.3fr]">
                {{-- Visi --}}
                <div class="relative overflow-hidden rounded-3xl border border-violet-200 bg-white p-8 sm:p-10 shadow-sm dark:border-[#8b5cf6]/30 dark:bg-[#0A1128] group">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-violet-100/50 dark:bg-[#8b5cf6]/20 blur-[50px] group-hover:bg-violet-200/50 dark:group-hover:bg-[#8b5cf6]/30 transition-colors"></div>
                    <div class="relative z-10">
                        <div class="grid h-14 w-14 place-items-center rounded-2xl bg-violet-50 text-[#8b5cf6] dark:bg-[#8b5cf6]/20 ring-1 ring-violet-200 dark:ring-[#8b5cf6]/40 mb-8">
                            <i class="fas fa-eye text-2xl"></i>
                        </div>
                        <h3 class="text-sm font-bold uppercase tracking-widest text-[#8b5cf6] mb-4">Visi</h3>
                        <p class="text-2xl font-bold leading-snug text-slate-900 dark:text-white">
                            Menjadi komunitas teknologi terdepan yang melahirkan inovator digital masa depan.
                        </p>
                    </div>
                </div>

                {{-- Misi --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 shadow-sm dark:border-white/10 dark:bg-[#0A1128] dark:shadow-none">
                    <div class="grid h-14 w-14 place-items-center rounded-2xl bg-cyan-50 text-[#06b6d4] dark:bg-[#06b6d4]/20 ring-1 ring-cyan-200 dark:ring-[#06b6d4]/40 mb-8">
                        <i class="fas fa-route text-2xl"></i>
                    </div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-[#06b6d4] mb-6">Misi</h3>
                    <ul class="space-y-5">
                        @foreach($missions as $index => $misi)
                            <li class="flex gap-4">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-cyan-50 text-xs font-bold text-[#06b6d4] dark:bg-[#06b6d4]/20 dark:text-[#22d3ee]">{{ $index + 1 }}</span>
                                <span class="text-sm leading-relaxed text-slate-700 dark:text-slate-300 pt-1">{{ $misi }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        {{-- ═══════════════════ 04. PROGRAM UNGGULAN (PDF) ═══════════════════ --}}
        <section id="program" class="pt-16 border-t border-slate-200 dark:border-white/5 ukm-reveal">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Tiga Program Kerja Unggulan</h2>
                    <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Inisiatif utama UKM Pemrograman untuk membangun ekosistem, karya, dan jejaring.</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-5 py-2.5 text-xs font-bold text-amber-700 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                    <i class="fas fa-star"></i> Berdasarkan PDF Strategis
                </span>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                @foreach($mainPrograms as $prog)
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-8 transition-all duration-300 hover:-translate-y-2 group dark:border-white/10 dark:bg-[#0A1128]" 
                         style="--hover-color: {{ $prog['color'] }};">
                        <div class="absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-10 dark:group-hover:opacity-10" style="background: linear-gradient(to bottom right, var(--hover-color), transparent);"></div>
                        <div class="relative z-10">
                            <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-2xl ring-1 shadow-sm group-hover:scale-110 transition-transform" 
                                 style="background-color: {{ $prog['color'] }}15; color: {{ $prog['color'] }}; border-color: {{ $prog['color'] }}30;">
                                <i class="{{ $prog['icon'] }} text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">{{ $prog['title'] }}</h3>
                            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 mb-6">{{ $prog['desc'] }}</p>
                            
                            <div class="flex flex-wrap gap-2">
                                @foreach($prog['tags'] as $tag)
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-white/5 dark:text-slate-300">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ═══════════════════ 05. STRUKTUR ORGANISASI (BAGAN) ═══════════════════ --}}
        <section id="struktur" class="pt-16 border-t border-slate-200 dark:border-white/5 ukm-reveal">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Struktur Organisasi</h2>
                <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Tata kelola UKM Pemrograman UNICODE periode aktif.</p>
            </div>

            <div class="mx-auto max-w-5xl">
                {{-- Core Level --}}
                <div class="flex flex-col items-center">
                    <div class="w-full max-w-xs rounded-2xl border border-[#06b6d4]/50 bg-white p-5 text-center shadow-[0_4px_20px_rgba(6,182,212,0.15)] dark:bg-[#0A1128] relative z-10">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-cyan-50 text-[#06b6d4] mb-3 ring-1 ring-cyan-200 dark:bg-[#06b6d4]/20 dark:ring-[#06b6d4]/50">
                            <i class="{{ $structure['core'][0]['icon'] }} text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $structure['core'][0]['title'] }}</h3>
                        <p class="text-xs text-[#06b6d4] mt-1">{{ $structure['core'][0]['sub'] }}</p>
                    </div>

                    <div class="h-8 w-px connector-line"></div>

                    <div class="w-full max-w-xs rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm dark:border-white/10 dark:bg-[#0A1128] dark:shadow-none">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-500 mb-3 dark:bg-white/5 dark:text-slate-300">
                            <i class="{{ $structure['core'][1]['icon'] }} text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $structure['core'][1]['title'] }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $structure['core'][1]['sub'] }}</p>
                    </div>

                    <div class="h-8 w-px connector-line"></div>

                    <div class="w-full max-w-xs rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm dark:border-white/10 dark:bg-[#0A1128] dark:shadow-none">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-500 mb-3 dark:bg-white/5 dark:text-slate-300">
                            <i class="{{ $structure['core'][2]['icon'] }} text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $structure['core'][2]['title'] }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $structure['core'][2]['sub'] }}</p>
                    </div>
                </div>

                {{-- Garis Percabangan --}}
                <div class="relative flex justify-center h-10">
                    <div class="absolute top-0 h-full w-px connector-line"></div>
                    <div class="absolute top-full left-1/4 right-1/4 h-px connector-line"></div>
                    <div class="absolute top-full left-1/4 h-6 w-px connector-line"></div>
                    <div class="absolute top-full right-1/4 h-6 w-px connector-line"></div>
                </div>

                {{-- Divisions --}}
                <div class="grid sm:grid-cols-2 gap-8 mt-8">
                    @foreach($structure['divisions'] as $div)
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-full rounded-2xl border bg-white p-5 text-center shadow-sm dark:bg-[#0A1128] dark:shadow-none" style="border-color: {{ $div['color'] }}50;">
                                <div class="mx-auto grid h-10 w-10 place-items-center rounded-lg mb-2" style="background-color: {{ $div['color'] }}15; color: {{ $div['color'] }};">
                                    <i class="{{ $div['icon'] }}"></i>
                                </div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-md">{{ $div['title'] }}</h3>
                                <p class="text-[11px] mt-1 uppercase tracking-wider font-semibold" style="color: {{ $div['color'] }};">{{ $div['sub'] }}</p>
                            </div>
                            <div class="h-5 w-px connector-line"></div>

                            <div class="w-full rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm dark:border-white/10 dark:bg-[#0A1128] dark:shadow-none">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $div['asisten'] }}</p>
                            </div>
                            <div class="h-5 w-px connector-line"></div>

                            <div class="w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center dark:border-white/20 dark:bg-white/[0.02]">
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $div['anggota'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═══════════════════ 06. TECH STACK ═══════════════════ --}}
        <section id="tech-stack" class="pt-16 border-t border-slate-200 dark:border-white/5 ukm-reveal">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Tech Stack yang Dipelajari</h2>
                <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Eksplorasi teknologi dari hulu ke hilir.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach($techStack as $tech)
                    <span class="rounded-full border border-slate-200 bg-white px-5 py-2 text-sm font-bold text-slate-700 shadow-sm transition-all hover:-translate-y-1 hover:border-[#06b6d4] hover:text-[#06b6d4] dark:border-white/10 dark:bg-[#0A1128] dark:text-slate-300 dark:hover:border-[#06b6d4]/50 dark:hover:text-[#22d3ee]">
                        {{ $tech }}
                    </span>
                @endforeach
            </div>
        </section>

        {{-- ═══════════════════ 07. PENGURUS INTI ═══════════════════ --}}
        <section id="pengurus" class="pt-16 border-t border-slate-200 dark:border-white/5 ukm-reveal">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Pengurus Inti</h2>
                <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Kabinet periode aktif UKM Pemrograman UNICODE.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($members as $m)
                    <div class="group rounded-3xl border border-slate-200 bg-white p-6 text-center transition-all hover:-translate-y-1 hover:border-[#06b6d4] hover:shadow-lg dark:border-white/10 dark:bg-[#0A1128] dark:hover:border-[#06b6d4]/30 dark:hover:bg-white/[0.02]">
                        <div class="relative mx-auto w-24 h-24 mb-4">
                            <div class="absolute inset-0 rounded-full bg-[#06b6d4] opacity-20 blur-md group-hover:opacity-40 transition-opacity"></div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($m['name']) }}&background=0D8ABC&color=fff&size=128" alt="{{ $m['name'] }}" class="relative h-24 w-24 rounded-full border-4 border-white dark:border-[#060B19] object-cover shadow-sm">
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $m['name'] }}</h3>
                        <p class="text-sm font-semibold text-[#06b6d4] mt-1">{{ $m['role'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">{{ $m['nim'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

    {{-- FOOTER CTA --}}
    <div class="border-t border-slate-200 bg-white dark:border-white/5 dark:bg-[#0A1128] transition-colors duration-300">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <a href="{{ $ukmUrl }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-[#06b6d4] dark:text-slate-400 dark:hover:text-[#22d3ee]">
                <i class="fas fa-arrow-left"></i> Kembali ke Direktori UKM
            </a>
            <p class="text-xs text-slate-400 dark:text-slate-500">© {{ date('Y') }} UKM Pemrograman UNICODE — Universitas Bhakti Asih</p>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    /* 1. TYPEWRITER ANIMATION */
    const textUkm = "UKM";
    const textPemrograman = "Pemrograman";
    
    const elUkm = document.getElementById('typewriter-ukm');
    const elPemrograman = document.getElementById('typewriter-pemrograman');
    const cursorUkm = document.getElementById('cursor-ukm');
    const cursorPemrograman = document.getElementById('cursor-pemrograman');
    
    let i = 0;
    let j = 0;
    
    function typeUkm() {
        if (i < textUkm.length) {
            elUkm.textContent += textUkm.charAt(i);
            i++;
            setTimeout(typeUkm, 150);
        } else {
            cursorUkm.classList.add('hidden');
            cursorPemrograman.classList.remove('hidden');
            setTimeout(typePemrograman, 300);
        }
    }
    
    function typePemrograman() {
        if (j < textPemrograman.length) {
            elPemrograman.textContent += textPemrograman.charAt(j);
            j++;
            setTimeout(typePemrograman, 100);
        }
    }
    
    setTimeout(typeUkm, 500);

    /* 2. REVEAL ON SCROLL */
    const revealItems = document.querySelectorAll('.ukm-reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        
        revealItems.forEach(item => observer.observe(item));
    } else {
        revealItems.forEach(item => item.classList.add('is-visible'));
    }
});
</script>
@endpush