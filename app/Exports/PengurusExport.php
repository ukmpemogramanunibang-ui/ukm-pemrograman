<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengurusExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return ['Nama', 'NIM', 'Email', 'Level', 'Universitas', 'Jurusan', 'Jabatan', 'Status'];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->nim,
            $user->email,
            optional($user->level)->name ?? '-',
            optional($user->university)->name ?? '-',
            optional($user->major)->name ?? '-',
            $user->jabatans->pluck('name')->join(', ') ?: '-',
            $user->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}