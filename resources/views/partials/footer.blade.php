{{-- resources/views/partials/footer.blade.php --}}
<footer class="bg-gradient-to-r from-midnight via-deepblue to-midnight text-white pt-16 pb-8 mt-20 border-t border-cyan/20 dark:border-gray-800 transition-colors duration-500">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

            {{-- Tentang UKM --}}
            <div>
                <h4 class="text-2xl font-bold mb-4 flex items-center">
                    <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo" class="h-10 w-10 rounded-full mr-3 border border-white/30">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan to-electric">UNICODE</span>
                </h4>
                <p class="text-gray-400 leading-relaxed mb-4">
                    Komunitas pengembang teknologi, inovator, dan pembelajar sejati. 
                    Kami berkomitmen mencetak talenta digital yang siap bersaing di era industri 4.0.
                </p>
                <div class="flex space-x-4">
                    <a href="https://instagram.com/ukm_pemrograman" target="_blank" class="text-gray-400 hover:text-pink-400 transition-colors text-2xl"><i class="fab fa-instagram"></i></a>
                    <a href="https://youtube.com/c/ukmpemrograman" target="_blank" class="text-gray-400 hover:text-red-500 transition-colors text-2xl"><i class="fab fa-youtube"></i></a>
                    <a href="https://github.com/ukm-pemrograman" target="_blank" class="text-gray-400 hover:text-white transition-colors text-2xl"><i class="fab fa-github"></i></a>
                    <a href="https://twitter.com/ukmprogramming" target="_blank" class="text-gray-400 hover:text-blue-400 transition-colors text-2xl"><i class="fab fa-twitter"></i></a>
                    <a href="https://linkedin.com/company/ukm-pemrograman" target="_blank" class="text-gray-400 hover:text-blue-500 transition-colors text-2xl"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            {{-- Menu Navigasi --}}
            <div>
                <h5 class="text-lg font-semibold mb-4 text-cyan">Menu</h5>
                <ul class="space-y-3 text-gray-400">
                    <li><a href="/" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Beranda</a></li>
                    <li><a href="#tentang" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Tentang</a></li>
                    <li><a href="#divisi" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Divisi</a></li>
                    <li><a href="#struktur" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Struktur</a></li>
                    <li><a href="#prestasi" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Prestasi</a></li>
                    <li><a href="#galeri" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Galeri</a></li>
                    <li><a href="#kontak" class="hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-cyan"></i> Kontak</a></li>
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h5 class="text-lg font-semibold mb-4 text-cyan">Kontak</h5>
                <ul class="space-y-3 text-gray-400">
                    <li><i class="fas fa-envelope w-5 text-cyan"></i> <a href="mailto:ukm@example.com" class="hover:text-white transition-colors">ukm@example.com</a></li>
                    <li><i class="fas fa-phone-alt w-5 text-cyan"></i> <a href="tel:+6285123942400" class="hover:text-white transition-colors">+62 851-2394-2400</a></li>
                    <li><i class="fas fa-map-marker-alt w-5 text-cyan"></i> Kampus Universitas Bhakti Asih, Tangerang</li>
                </ul>
            </div>

            {{-- Maps Lokasi --}}
            <div>
                <h5 class="text-lg font-semibold mb-4 text-cyan">Lokasi Kami</h5>
                <a href="https://www.google.com/maps/search/Universitas+Bhakti+Asih+Tangerang" target="_blank" 
                   class="block rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow duration-300 border border-gray-700 group">
                    <div class="relative h-40 w-full bg-gray-700">
                        {{-- Hapus parameter yang tidak valid pada src maps jika perlu --}}
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.531239109446!2d106.629868!3d-6.198722!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f8c8b2e1c9f3%3A0x3f6b1e9e8c2b4d5a!2sUniversitas%20Bhakti%20Asih!5e0!3m2!1sid!2sid!4v1647567890123" 
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" 
                            class="absolute inset-0 w-full h-full group-hover:scale-105 transition-transform duration-500">
                        </iframe>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-2">
                            <span class="text-white text-sm font-semibold bg-black/50 px-3 py-1 rounded-full backdrop-blur-sm">
                                <i class="fas fa-map-pin mr-1"></i> Lihat di Google Maps
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-cyan/20 mt-12 pt-6 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan to-electric font-semibold">UKM Pemrograman UNICODE</span> – Universitas Bhakti Asih. All Rights Reserved.</p>
            <p class="mt-1 text-xs">Design with ❤️ by UNICODE Team | <a href="#" class="hover:text-white transition-colors">Privacy Policy</a> | <a href="#" class="hover:text-white transition-colors">Terms & Conditions</a></p>
        </div>
    </div>
</footer>