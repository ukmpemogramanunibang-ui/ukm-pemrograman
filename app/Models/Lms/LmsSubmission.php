<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LmsSubmission extends Model
{
    protected $table = 'lms_submissions';

    protected $fillable = [
        'uuid', 'item_id', 'user_id', 'answer_text', 'answer_link', 'note',
        'status', 'submitted_at', 'reviewed_at', 'reviewed_by',
        'score', 'feedback', 'is_late', 'is_resubmit',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
        'is_late'      => 'boolean',
        'is_resubmit'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->uuid ??= (string) Str::uuid());
    }

    public function item()   { return $this->belongsTo(LmsItem::class, 'item_id'); }
    public function user()   { return $this->belongsTo(User::class); }
    public function files()  { return $this->hasMany(LmsSubmissionFile::class, 'submission_id'); }
    public function reviewer(){ return $this->belongsTo(User::class, 'reviewed_by'); }
}