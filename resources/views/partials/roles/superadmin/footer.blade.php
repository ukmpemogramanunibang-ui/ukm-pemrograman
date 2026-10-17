{{-- resources/views/partials/roles/superadmin/footer.blade.php --}}
<footer class="border-t border-gray-200 dark:border-gray-800
               bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm
               px-4 md:px-6 py-3 flex-shrink-0">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2
                text-[11px] text-gray-500 dark:text-gray-400">
        <p>&copy; {{ date('Y') }} <strong class="text-gray-700 dark:text-gray-200">UNICODE</strong> — Panel Superadmin</p>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1">
                <i class="fas fa-user-shield text-purple-500"></i> Superadmin
            </span>
            <span class="text-gray-300 dark:text-gray-600">•</span>
            <span class="flex items-center gap-1">
                <i class="fas fa-code text-purple-400"></i> v1.0
            </span>
        </div>
    </div>
</footer>