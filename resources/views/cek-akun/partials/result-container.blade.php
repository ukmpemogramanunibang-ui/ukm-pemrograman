<section class="mt-6">

    <div class="flex items-center justify-between mb-3">
        <div>
            <h2 class="text-lg md:text-xl font-extrabold text-white">Hasil Pemeriksaan</h2>
            <p class="text-xs text-slate-500">Hasil muncul otomatis setelah identifier diverifikasi.</p>
        </div>
        <button type="button" id="clearResultsBtn" class="secondary-btn text-xs">Bersihkan</button>
    </div>

    <div id="systemStatus"
         role="status"
         aria-live="polite"
         class="hidden mb-4 rounded-2xl border px-4 py-3 text-sm"></div>

    <div id="resultsContainer"
         role="region"
         aria-live="polite"
         aria-label="Hasil pemeriksaan akun"
         class="space-y-5">
        <div class="glass-panel rounded-3xl empty-result p-8">
            <div class="text-center max-w-lg">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-800/70 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-id-card text-2xl text-slate-500"></i>
                </div>
                <h3 class="font-bold text-white mb-2">Belum ada pemeriksaan</h3>
                <p class="text-sm text-slate-500 leading-6">
                    Isi form di atas atau lakukan scan untuk memulai pengecekan.
                </p>
            </div>
        </div>
    </div>

</section>