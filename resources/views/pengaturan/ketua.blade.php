@extends('layouts.roles.ketua')
@section('title', 'Pengaturan – UNICODE NEXT LEVEL')
@section('content')

<div class="space-y-8 max-w-7xl mx-auto pb-20">
    
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white neon-glow">⚙️ Pengaturan Sistem</h1>
            <p class="text-gray-400 text-sm mt-1">Kelola pengaturan organisasi, keamanan, dan personalisasi dashboard Ketua.</p>
        </div>
        <button class="px-6 py-3 bg-gradient-to-r from-unic-blue to-unic-purple rounded-xl text-white font-bold hover:scale-105 transition shadow-lg shadow-unic-blue/20">
            <i class="fas fa-save mr-2"></i> Simpan Perubahan
        </button>
    </div>

    <!-- Layout Grid: 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri (Menu Navigasi Pengaturan) -->
        <div class="lg:col-span-1 space-y-4">
            <div class="glass rounded-2xl p-4">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2">Menu Pengaturan</p>
                <div class="space-y-1">
                    <a href="#profil" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl bg-unic-blue/10 text-unic-cyan border border-unic-cyan/20 transition">
                        <i class="fas fa-user-circle w-5"></i> <span class="font-medium">Profil Ketua</span>
                    </a>
                    <a href="#keamanan" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 transition">
                        <i class="fas fa-shield-alt w-5"></i> <span class="font-medium">Keamanan & PIN</span>
                    </a>
                    <a href="#organisasi" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 transition">
                        <i class="fas fa-building w-5"></i> <span class="font-medium">Data Organisasi</span>
                    </a>
                    <a href="#tema" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 transition">
                        <i class="fas fa-palette w-5"></i> <span class="font-medium">Tema & Personalisasi</span>
                    </a>
                    <a href="#roles" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-300 hover:bg-white/5 transition">
                        <i class="fas fa-user-lock w-5"></i> <span class="font-medium">Manajemen Hak Akses</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (Form Pengaturan) -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Bagian 1: Profil Ketua -->
            <div id="profil" class="glass rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-unic-cyan/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 relative z-10"><i class="fas fa-user-circle text-unic-cyan"></i> Profil Ketua</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" value="Muhammad Ibni" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-cyan transition mt-1 placeholder-gray-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Email</label>
                        <input type="email" value="ibni@unicode.unibang.ac.id" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-cyan transition mt-1 placeholder-gray-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor WhatsApp</label>
                        <input type="text" value="+62 851-2394-2400" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-cyan transition mt-1 placeholder-gray-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Divisi / Jabatan</label>
                        <input type="text" value="Ketua UKM UNICODE" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-cyan transition mt-1 placeholder-gray-500">
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Keamanan & PIN -->
            <div id="keamanan" class="glass rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-unic-red/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 relative z-10"><i class="fas fa-shield-alt text-unic-red"></i> Keamanan & PIN Verifikasi</h2>
                
                <div class="space-y-4 relative z-10">
                    <div class="bg-unic-red/10 border border-unic-red/20 rounded-xl p-4 text-sm text-unic-red">
                        <i class="fas fa-info-circle mr-2"></i> PIN ini akan digunakan saat Anda melakukan Login Manual atau melakukan perubahan data penting organisasi.
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">PIN Keamanan (6 Digit)</label>
                            <input type="password" value="••••••" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-red transition mt-1 tracking-widest">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Konfirmasi PIN</label>
                            <input type="password" value="••••••" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-red transition mt-1 tracking-widest">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Data Organisasi -->
            <div id="organisasi" class="glass rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-unic-purple/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 relative z-10"><i class="fas fa-building text-unic-purple"></i> Data Organisasi UKM</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Organisasi</label>
                        <input type="text" value="UKM Pemrograman UNICODE UNIBANG" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-purple transition mt-1">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tahun Berdiri</label>
                        <input type="text" value="2022" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-purple transition mt-1">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah Anggota Saat Ini</label>
                        <input type="text" value="245 Mahasiswa" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-unic-purple transition mt-1">
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Tema & Personalisasi -->
            <div id="tema" class="glass rounded-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-unic-orange/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 relative z-10"><i class="fas fa-palette text-unic-orange"></i> Tema Dashboard</h2>
                
                <div class="space-y-6 relative z-10">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-3">Pilih Mode</label>
                        <div class="flex gap-4">
                            <button onclick="toggleDarkMode()" class="flex-1 glass p-4 rounded-xl text-center hover:bg-unic-blue/10 transition border border-white/10">
                                <i class="fas fa-moon text-xl block mb-1"></i>
                                <span class="text-sm">Dark Mode</span>
                            </button>
                            <button onclick="toggleDarkMode()" class="flex-1 glass p-4 rounded-xl text-center hover:bg-unic-orange/10 transition border border-white/10">
                                <i class="fas fa-sun text-xl block mb-1 text-unic-orange"></i>
                                <span class="text-sm">Light Mode</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection