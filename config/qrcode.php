<?php

return [
    'size' => env('QR_SIZE', 400),
    'margin' => env('QR_MARGIN', 20),
    'logo_path' => public_path('images/logo-unicode.png'),
    'logo_width' => env('QR_LOGO_WIDTH', 80),
    'logo_height' => env('QR_LOGO_HEIGHT', 80),
];