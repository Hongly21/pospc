<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bakong NBC API Configuration
    |--------------------------------------------------------------------------
    | Get your token from: https://api-bakong.nbc.gov.kh
    | Your bakong account format: yourname@yourbank  e.g. john@wing
    */

    'base_url'      => env('KHQR_BASE_URL', 'https://api-bakong.nbc.gov.kh'),
    'token'         => env('KHQR_TOKEN', ''),
    'account'       => env('KHQR_ACCOUNT', ''),       // e.g. yourname@wing
    'merchant_name' => env('KHQR_MERCHANT_NAME', 'My Shop'),
    'store_label'   => env('KHQR_STORE_LABEL', 'POS'),
    'phone'         => env('KHQR_PHONE', ''),
];
