{{--
|--------------------------------------------------------------------------
| Financial Submenu — Partial
|--------------------------------------------------------------------------
| Partial ini dipakai di SEMUA halaman owner.financial.*
| Tujuan: agar Dashboard, Skema Biaya, Tagihan, Pembayaran, dll
| bisa saling berpindah tanpa harus lewat sidebar.
|
| Cara pakai:
|   @include('owner.financial._partials.submenu', ['active' => 'fee-plans'])
|
| Nilai $active yang valid:
|   dashboard | generations | fee-plans | invoices | payments |
|   penalty-rules | points | campaigns | holidays |
|   academic-periods | reports | reactivations
|--------------------------------------------------------------------------
--}}

@php
    $active = $active ?? 'dashboard';

    // Definisi menu utama (yang selalu ada)
    $menuItems = [
        [
            'key'   => 'dashboard',
            'label' => 'Dashboard',
            'icon'  => 'fas fa-chart-pie',
            'route' => 'owner.financial.dashboard',
        ],
        [
            'key'   => 'generations',
            'label' => 'Generasi',
            'icon'  => 'fas fa-layer-group',
            'route' => 'owner.financial.generations.index',
        ],
        [
            'key'   => 'fee-plans',
            'label' => 'Skema Biaya',
            'icon'  => 'fas fa-file-invoice-dollar',
            'route' => 'owner.financial.fee-plans.index',
        ],
        [
            'key'   => 'invoices',
            'label' => 'Tagihan',
            'icon'  => 'fas fa-file-invoice',
            'route' => 'owner.financial.invoices.index',
        ],
        [
            'key'   => 'payments',
            'label' => 'Pembayaran',
            'icon'  => 'fas fa-money-bill-wave',
            'route' => 'owner.financial.payments.index',
        ],
        [
            'key'   => 'penalty-rules',
            'label' => 'Denda',
            'icon'  => 'fas fa-exclamation-triangle',
            'route' => 'owner.financial.penalty-rules.index',
        ],
        [
            'key'   => 'points',
            'label' => 'Poin',
            'icon'  => 'fas fa-star',
            'route' => 'owner.financial.points.index',
        ],
        [
            'key'   => 'campaigns',
            'label' => 'Kampanye',
            'icon'  => 'fas fa-bullhorn',
            'route' => 'owner.financial.campaigns.index',
        ],
        [
            'key'   => 'reports',
            'label' => 'Laporan',
            'icon'  => 'fas fa-chart-bar',
            'route' => 'owner.financial.reports.index',
        ],
    ];

    // Filter otomatis: hanya tampilkan menu yang route-nya terdaftar
    $menuItems = array_filter($menuItems, function ($item) {
        return \Illuminate\Support\Facades\Route::has($item['route']);
    });
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-4 overflow-x-auto">
    <nav class="flex min-w-max" aria-label="Submenu Keuangan">
        @foreach($menuItems as $item)
            @php
                $isActive = $active === $item['key'];
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap
                      {{ $isActive
                          ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10'
                          : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>