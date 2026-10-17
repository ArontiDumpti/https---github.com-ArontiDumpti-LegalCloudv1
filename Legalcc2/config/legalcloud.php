<?php

return [
    'api_mode' => env('LEGALCLOUD_API_MODE', 'api'),
    'api_token_ttl_days' => (int) env('LEGALCLOUD_API_TOKEN_TTL_DAYS', 30),
    'ai' => [
        'enabled' => (bool) env('LEGALCLOUD_LLM_ENABLED', false),
        'provider' => env('LEGALCLOUD_LLM_PROVIDER', 'openai'),
    ],
    'ocr' => [
        'provider' => env('LEGALCLOUD_OCR_PROVIDER', 'native'),
    ],
    'embeddings' => [
        'enabled' => (bool) env('LEGALCLOUD_EMBEDDINGS_ENABLED', false),
        'provider' => env('LEGALCLOUD_EMBEDDINGS_PROVIDER', 'openai'),
    ],
    'vector' => [
        'enabled' => (bool) env('VECTOR_DB_ENABLED', false),
        'connection' => env('VECTOR_DB_CONNECTION', 'vector'),
        'dimensions' => (int) env('VECTOR_EMBEDDING_DIMENSIONS', 1536),
        'embedding_model' => env('VECTOR_EMBEDDING_MODEL', 'text-embedding-3-small'),
    ],
    'permissions' => [
        'users.manage',
        'cases.read',
        'cases.write',
        'documents.read',
        'documents.write',
        'documents.process',
        'categories.manage',
        'search.use',
        'assistant.use',
        'audit.read',
        'notifications.read',
    ],
    'roles' => [
        'administrator' => [
            'label' => 'Administrador',
            'permissions' => ['users.manage', 'cases.read', 'cases.write', 'documents.read', 'documents.write', 'documents.process', 'categories.manage', 'search.use', 'assistant.use', 'audit.read', 'notifications.read'],
        ],
        'records_manager' => [
            'label' => 'Responsable documental',
            'permissions' => ['cases.read', 'cases.write', 'documents.read', 'documents.write', 'documents.process', 'categories.manage', 'search.use', 'assistant.use', 'audit.read', 'notifications.read'],
        ],
        'analyst' => [
            'label' => 'Especialista / analista',
            'permissions' => ['cases.read', 'cases.write', 'documents.read', 'documents.write', 'documents.process', 'search.use', 'assistant.use', 'notifications.read'],
        ],
        'consultant' => [
            'label' => 'Usuario consultor',
            'permissions' => ['cases.read', 'documents.read', 'search.use', 'assistant.use', 'notifications.read'],
        ],
    ],
];