<?php

return [
    'csp_enabled' => env('SECURITY_CSP_ENABLED', true),

    'csp' => [
        'default-src' => "'self'",
        'script-src'  => "'self' 'nonce-{nonce}' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com",
        'style-src'   => "'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com",
        'font-src'    => "'self' https://fonts.gstatic.com data:",
        'img-src'     => "'self' data: https:",
        'connect-src' => "'self' ws: wss:",
        'frame-ancestors' => "'none'",
    ],

    'headers' => [
        'X-Content-Type-Options'  => 'nosniff',
        'X-Frame-Options'         => 'DENY',
        'Referrer-Policy'         => 'strict-origin-when-cross-origin',
        'Permissions-Policy'      => 'geolocation=(), microphone=(), camera=()',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
    ],
];