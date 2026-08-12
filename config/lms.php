<?php

return [
    'currency_code' => env('CURRENCY_CODE', 'BDT'),
    'currency_symbol' => env('CURRENCY_SYMBOL', '৳'),
    'tax_percent' => env('PLATFORM_TAX_PERCENT', 0),

    'roles' => [
        'admin' => 'Administrator',
        'teacher' => 'Teacher',
        'manager' => 'Manager',
        'student' => 'Student',
    ],

    'pagination' => [
        'courses' => 9,
        'books' => 9,
        'admin_tables' => 15,
    ],

    'sslcommerz' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
    ],
];
