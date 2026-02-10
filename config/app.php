<?php

declare(strict_types = 1)
;

return [
    'name' => getenv('APP_NAME') ?: 'Gestor IA',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'upload_dir' => getenv('UPLOAD_DIR') ?: 'uploads',

    // Configuração OpenCode Zen
    'llm' => [
        'api_url' => 'https://opencode.ai/zen/v1/chat/completions',
        'api_key' => 'sk-nhzogvPrZXkIvWRYQTiuSnaSHz3pl47c4mFl7fU7JAIvqGWI0a00045FqZeLPnFd', // Em produção, usar getenv('LLM_API_KEY')
        'model' => 'gpt-5-nano',
        'context_limit' => 10,
        'debug_mode' => true,
    ],
];
