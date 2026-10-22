<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialCampaign extends Model
{
    use SoftDeletes;

    /**
     * Nama tabel (opsional, karena Laravel otomatis pakai plural)
     */
    protected $table = 'financial_campaigns';

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'amount',
        'is_required',
        'target_type',
        'target_data',       // jika ada, untuk menyimpan target spesifik
        'start_date',
        'end_date',
        'due_date',
        'allow_installment',
        'allow_pre_payment',
        'max_payment',
        'status',
        'created_by',
    ];

    /**
     * Casting tipe data otomatis.
     */
    protected $casts = [
        'is_required' => 'boolean',
        'allow_installment' => 'boolean',
        'allow_pre_payment' => 'boolean',
        'amount' => 'integer',
        'max_payment' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'due_date' => 'date',
        'target_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke user pembuat.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope untuk kampanye aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Cek apakah kampanye masih berlaku.
     */
    public function isValid()
    {
        return $this->status === 'active'
            && $this->start_date <= now()
            && ($this->end_date === null || $this->end_date >= now());
    }

    /**
     * Mendapatkan label status untuk tampilan.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'active' => 'Aktif',
            'closed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Mendapatkan label kategori.
     */
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'id_card' => 'ID Card',
            'event' => 'Event',
            'reunion' => 'Reuni',
            'merchandise' => 'Merchandise',
            'registration' => 'Pendaftaran',
            'other' => 'Lainnya',
        ];
        return $labels[$this->category] ?? $this->category;
    }
}