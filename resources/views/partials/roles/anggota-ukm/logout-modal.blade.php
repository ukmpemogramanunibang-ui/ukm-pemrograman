{{-- resources/views/partials/roles/anggota-ukm/logout-modal.blade.php --}}
<div id="logout-confirm-modal"
     class="fixed inset-0 z-[100] hidden opacity-0 transition-opacity duration-300 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">

    <div class="transform scale-95 transition-transform duration-300 bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-sm w-full p-6 border border-gray-200 dark:border-gray-800">

        <div class="flex flex-col items-center text-center gap-3">
            <div class="w-16 h-16 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-500 flex items-center justify-center">
                <i class="fas fa-sign-out-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Konfirmasi Logout</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Apakah Anda yakin ingin keluar dari akun ini?
            </p>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="window.closeLogoutModal()"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium text-sm transition-all duration-200">
                Batal
            </button>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
               class="flex-1 px-4 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-medium text-sm transition-all duration-200 text-center">
                Logout
            </a>
        </div>

        <form id="logout-form-sidebar" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </div>
</div>