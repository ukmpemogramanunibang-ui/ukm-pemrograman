<div id="logout-confirm-modal"
     class="hidden fixed inset-0 z-[100] flex items-center justify-center
            bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 px-4">
    <div class="w-full max-w-md rounded-2xl shadow-2xl
                bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800
                p-6 transform scale-95 transition-transform duration-300">
        <h3 class="font-bold text-gray-800 dark:text-white mb-2">Konfirmasi Logout</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Anda yakin ingin keluar dari akun Anggota Umum?</p>
        <div class="flex items-center justify-end gap-2 mt-6">
            <button type="button" onclick="window.closeLogoutModal()"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 dark:bg-gray-800
                           text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                Batal
            </button>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold
                               bg-red-500 hover:bg-red-600 text-white
                               shadow-lg shadow-red-500/20 active:scale-95 transition-all">
                    Ya, Logout
                </button>
            </form>
        </div>
    </div>
</div>