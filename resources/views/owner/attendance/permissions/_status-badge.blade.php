@php
    $status = $p->status ?? 'pending';
    $map = [
        'pending'  => ['label' => 'Menunggu', 'color' => 'amber',   'icon' => 'fa-hourglass-half'],
        'approved' => ['label' => 'Disetujui','color' => 'emerald', 'icon' => 'fa-check-circle'],
        'rejected' => ['label' => 'Ditolak',  'color' => 'red',     'icon' => 'fa-xmark-circle'],
    ];
    $s = $map[$status] ?? $map['pending'];
@endphp
<span class="inline-flex items-center gap-1 text-[11px] font-bold
             px-2.5 py-1 rounded-full whitespace-nowrap
             bg-{{ $s['color'] }}-100 dark:bg-{{ $s['color'] }}-900/40
             text-{{ $s['color'] }}-700 dark:text-{{ $s['color'] }}-300">
    <i class="fas {{ $s['icon'] }}"></i> {{ $s['label'] }}
</span>