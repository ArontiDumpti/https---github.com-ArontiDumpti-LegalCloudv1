<?php

return [
    'api_mode' => env('LEGALCLOUD_API_MODE', 'demo'),
    'api_token_ttl_days' => (int) env('LEGALCLOUD_API_TOKEN_TTL_DAYS', 30),
    'ai' => [
        'enabled' => (bool) env('LEGALCLOUD_LLM_ENABLED', false),
        'provider' => env('LEGALCLOUD_LLM_PROVIDER', 'openai'),
    ],
    'vector' => [
        'enabled' => (bool) env('VECTOR_DB_ENABLED', false),
        'connection' => env('VECTOR_DB_CONNECTION', 'vector'),
        'dimensions' => (int) env('VECTOR_EMBEDDING_DIMENSIONS', 1536),
        'embedding_model' => env('VECTOR_EMBEDDING_MODEL', 'unset'),
    ],
];