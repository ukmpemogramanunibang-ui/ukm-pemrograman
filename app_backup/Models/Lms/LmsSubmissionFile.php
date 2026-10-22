<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsSubmissionFile extends Model
{
    protected $table = 'lms_submission_files';
    protected $fillable = ['submission_id', 'original_name', 'path', 'mime_type', 'size'];
    public function submission() { return $this->belongsTo(LmsSubmission::class, 'submission_id'); }
}