<?php

return array(
    'debug' => true,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'key' => 'YourSecretKey!!!',
    'providers' => array(
        'Illuminate\Filesystem\FilesystemServiceProvider',
        'Bernard\Laravel\BernardServiceProvider',
    ),
    'manifest' => storage_path().'/meta',
    'aliases' => array(
        'Producer' => 'Bernard\Laravel\Facades\Producer',
        'Consumer' => 'Bernard\Laravel\Facades\Consumer',
    ),

);
