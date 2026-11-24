<?php

return [
    'name'      => env('SHOP_NAME', 'Warung Saya'),
    // Nomor WA penjual, format internasional tanpa + (contoh: 6281234567890)
    'wa_number' => env('SHOP_WA', '6281234567890'),
    // Password login penjual
    'password'  => env('SELLER_PASSWORD'),
];
