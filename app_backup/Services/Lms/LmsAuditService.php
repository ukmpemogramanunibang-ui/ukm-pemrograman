<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsAuditLog;
use Illuminate\Support\Facades\Log;

class LmsAuditService
{
    public function log(
        string $action,
        $model,
        array $meta = [],
        ?string $reason = null
    ): void {
        try {
            if (class_exists(LmsAuditLog::class)) {
                LmsAuditLog::create([
                    'action'     => $action,
                    'model_type' => get_class($model),
                    'model_id'   => $model->id ?? null,
                    'causer_id'  => auth()->id(),
                    'meta'       => $meta,
                    'reason'     => $reason,
                ]);
                return;
            }
        } catch (\Throwable $e) {
            // fallback ke log
        }

        Log::info('LMS Audit: ' . $action, [
            'model'  => get_class($model),
            'id'     => $model->id ?? null,
            'meta'   => $meta,
            'reason' => $reason,
            'user'   => auth()->id(),
        ]);
    }
}