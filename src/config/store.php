<?php

return [
    'name' => env('STORE_NAME', 'Shineray Rio Branco'),
    'address' => env('STORE_ADDRESS', ''),
    'phone' => env('STORE_PHONE', ''),
    'whatsapp' => env('STORE_WHATSAPP', ''),
    'email' => env('STORE_EMAIL', ''),
    'sales_email' => env('STORE_SALES_EMAIL', env('STORE_EMAIL', '')),
    'instagram' => env('STORE_INSTAGRAM', ''),
];
