<?php

declare(strict_types=1);

return [
    'app_name' => 'Gạo Vi Sinh',
    'base_url' => getenv('APP_URL') ?: '',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'gao_vi_sinh',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];
