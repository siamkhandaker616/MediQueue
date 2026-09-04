<?php

return [
    'store_id'       => env('SSLCZ_STORE_ID', 'mediqueue_sandbox'),
    'store_password' => env('SSLCZ_STORE_PASSWORD', 'mediqueue@ssl'),
    'sandbox'        => env('SSLCZ_TESTMODE', true),
    'api_domain'     => env('SSLCZ_TESTMODE', true) 
                            ? 'https://sandbox.sslcommerz.com' 
                            : 'https://securepay.sslcommerz.com',
    'success_url'    => '/sslcommerz/success',
    'failed_url'     => '/sslcommerz/fail',
    'cancel_url'     => '/sslcommerz/cancel',
    'ipn_url'        => '/sslcommerz/ipn',
];