<nav class="flex flex-wrap gap-2 p-3 rounded-2xl bg-gray-100 dark:bg-gray-800">
    <a href="{{ route('ketua.pengaturan') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
              {{ request()->routeIs('ketua.pengaturan') ? 'bg-white dark:bg-gray-700 text-purple-600 dark:text-purple-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
        <i class="fas fa-user-circle"></i> Profil
    </a>
    <a href="{{ route('ketua.notifications') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
              text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
        <i class="fas fa-bell"></i> Notifikasi
    </a>
</nav>