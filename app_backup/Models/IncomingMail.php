<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingMail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_diterima',
        'pengirim',
        'perihal',
        'kategori',
        'tujuan',
        'dicatat_oleh',
        'status',
        'lampiran',
        'catatan',
        'diteruskan_kepada',
        'diteruskan_pada',
        'diarsipkan_pada',
    ];

    protected $casts = [
        'tanggal_surat'     => 'date',
        'tanggal_diterima'  => 'date',
        'diteruskan_pada'   => 'datetime',
        'diarsipkan_pada'   => 'datetime',
    ];

    // Relasi
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function diteruskanKe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diteruskan_kepada');
    }

    // Scope untuk filter
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'semua') {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeKategori($query, $kategori)
    {
        if ($kategori && $kategori !== 'semua') {
            return $query->where('kategori', $kategori);
        }
        return $query;
    }

    public function scopeBulan($query, $bulan)
    {
        if ($bulan) {
            return $query->whereMonth('tanggal_diterima', $bulan);
        }
        return $query;
    }

    public function scopeTahun($query, $tahun)
    {
        if ($tahun) {
            return $query->whereYear('tanggal_diterima', $tahun);
        }
        return $query;
    }

    public function scopeCari($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('nomor_surat', 'LIKE', "%{$keyword}%")
                  ->orWhere('pengirim', 'LIKE', "%{$keyword}%")
                  ->orWhere('perihal', 'LIKE', "%{$keyword}%");
            });
        }
        return $query;
    }

    // Helper status badge
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'baru' => 'bg-red-100 text-red-700',
            'dibaca' => 'bg-blue-100 text-blue-700',
            'diproses' => 'bg-yellow-100 text-yellow-700',
            'diteruskan' => 'bg-purple-100 text-purple-700',
            'selesai' => 'bg-green-100 text-green-700',
            'diarsipkan' => 'bg-gray-100 text-gray-700',
        ];
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }
}