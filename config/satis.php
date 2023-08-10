<?php

return [
    'name' => env('SATIS_NAME', 'iwaves/packagist'),
    'homepage' => env('APP_URL', 'http://127.0.0.1:8000'),
    'output_dir' => storage_path(env('SATIS_OUTPUT_DIR', 'app/satis')),
    'archive' => [
        'directory' => env('SATIS_ARCHIVE_DIR', 'dist'),
        'skip_dev' => env('SATIS_ARCHIVE_SKIP_DEV', false),
        'format' => env('SATIS_ARCHIVE_FORMAT', 'tar')
    ],
    'require_all' => env('SATIS_REQUIRE_ALL', true),
];
