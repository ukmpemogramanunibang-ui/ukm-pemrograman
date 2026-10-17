<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Fungsi ini akan dipanggil per baris jika menggunakan Excel langsung
        // Tapi kita sudah menggunakan metode manual di controller
    }
}