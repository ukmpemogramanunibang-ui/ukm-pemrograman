<?php
// config/features.php
return [
    'nfc-attendance'    => env('FEATURE_NFC_ATTENDANCE', true),
    'qr-attendance'     => env('FEATURE_QR_ATTENDANCE', true),
    'face-recognition'  => env('FEATURE_FACE_RECOGNITION', false),
    'financial-reports' => env('FEATURE_FINANCIAL_REPORTS', true),
    'export-excel'      => env('FEATURE_EXPORT_EXCEL', true),
    'export-pdf'        => env('FEATURE_EXPORT_PDF', true),
    'whatsapp-notify'   => env('FEATURE_WHATSAPP_NOTIFY', false),
    'email-notify'      => env('FEATURE_EMAIL_NOTIFY', true),
    'realtime-notify'   => env('FEATURE_REALTIME_NOTIFY', true),
    'maintenance-mode'  => env('FEATURE_MAINTENANCE_MODE', false),
];