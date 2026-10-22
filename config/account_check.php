<?php

return [
    'nfc_identifier_columns' => [
        'identifier',
        'uid',
        'nfc_uid',
        'nfc_identifier',
        'nfc_token',
        'tag_uid',
        'card_uid',
        'token',
        'credential_uid',
    ],

    'privacy_tier' => [
        'manual'  => 'limited',
        'barcode' => 'full',
        'upload'  => 'full',
        'nfc'     => 'full',
    ],

    'reference_prefix' => 'CHK',
];