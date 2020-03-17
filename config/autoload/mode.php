<?php

declare(strict_types=1);


return [
    'env' => env("APP_MODE", "production"),
    'app_key' => env('APP_KEY',"rand"),
    'app_access_token' => env('APP_ACCESS_TOKEN', 'rand_access_token'),
    'app_refresh_token' => env('APP_REFRESH_TOKEN', 'rand_refresh_token'),
];
