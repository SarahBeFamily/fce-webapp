<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['POST', 'GET', 'DELETE', 'PUT', '*'],

    'allowed_origins' => ['fce.test', 'fce.befamily.it', '192.168.178.225', '192.168.178.225:5174', '192.168.178.26', '192.168.178.26:5174', '192.168.178.26:5173', '192.168.1.140', '192.168.1.140:5174', '127.0.0.1:8000', 'localhost:5173'],

    'allowed_origins_patterns' => ['https://services.webtic.it', 'https://fce.winticstellar.com'],

    'allowed_headers' => ['*'],

    'exposed_headers' => false,

    'max_age' => false,

    'supports_credentials' => true,

];
