<?php

return [

    
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

   'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        // Tambahkan Guard Petugas
        'petugas' => [
            'driver' => 'sanctum', // Gunakan sanctum
            'provider' => 'petugas',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pengguna::class, // Atau User::class bawaan
        ],
        // Tambahkan Provider Petugas
        'petugas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Petugas::class,
        ],
    ],


    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
