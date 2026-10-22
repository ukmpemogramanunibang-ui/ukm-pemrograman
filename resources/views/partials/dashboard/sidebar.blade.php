{{-- Sidebar Dashboard --}}
<aside class="w-64 bg-white dark:bg-gray-800 h-screen shadow-md border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo" class="h-8 w-8 rounded-full">
            <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan to-blue-600">UNICODE</span>
        </a>
    </div>
    <nav class="p-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-home w-5 text-gray-500 dark:text-gray-400"></i>
            <span class="ml-3 font-medium">Dashboard</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-user-cog w-5 text-gray-500 dark:text-gray-400"></i>
            <span class="ml-3 font-medium">Pengaturan</span>
        </a>
        <button onclick="showLogoutModal(event)" class="flex items-center w-full text-left px-4 py-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-colors">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span class="ml-3 font-medium">Keluar</span>
        </button>
    </nav>
</aside>
