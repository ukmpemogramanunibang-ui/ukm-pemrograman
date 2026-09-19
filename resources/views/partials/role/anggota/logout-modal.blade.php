{{-- resources/views/partials/role/anggota/logout-modal.blade.php --}}
<div id="logout-confirm-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 text-red-600 dark:text-red-400 text-3xl">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Konfirmasi Keluar</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin keluar dari akun ini?</p>
            <div class="flex w-full gap-4">
                <button onclick="closeLogoutModal()" class="flex-1 py-2.5 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium">
                    Tidak
                </button>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-md shadow-red-500/20 transition">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showLogoutModal(event) {
        if (event) event.preventDefault();
        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            const content = modal.querySelector('.transform');
            if (content) {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }
        }, 10);
    }
    function closeLogoutModal() {
        const modal = document.getElementById('logout-confirm-modal');
        if (!modal) return;
        modal.classList.add('opacity-0');
        const content = modal.querySelector('.transform');
        if (content) {
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('logout-confirm-modal');
        if (modal && !modal.classList.contains('hidden') && e.target === modal) {
            closeLogoutModal();
        }
    });
</script>