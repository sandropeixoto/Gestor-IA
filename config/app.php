<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Gestor IA',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'upload_dir' => getenv('UPLOAD_DIR') ?: 'uploads',
];
