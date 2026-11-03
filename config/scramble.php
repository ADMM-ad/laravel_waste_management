<?php

return [
    'api_path' => 'api',
    'api_domain' => null,
    'export_path' => 'api.json',
    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],
    'openapi_version' => \Dedoc\Scramble\OpenApiVersion::V3_1,
    'info' => [
        'version' => env('API_VERSION', '0.0.1'),
        'description' => 'Waste Management API Documentation',
    ],
    'ui' => [
        'title' => 'Waste Management API Docs',
    ],
    'dev_tools' => [
        'enabled' => false,
    ],
    'renderer' => 'elements',
    'renderers' => [
        'elements' => [
            'view' => 'scramble::docs',
            'theme' => 'light',
            'hideTryIt' => false,
            'hideSchemas' => false,
            'logo' => '',
            'tryItCredentialsPolicy' => 'include',
            'layout' => 'responsive',
            'router' => 'hash',
        ],
    ],
    'servers' => null,
    'enum_cases_description_strategy' => 'description',
    'enum_cases_names_strategy' => false,
    'flatten_deep_query_parameters' => true,
    'middleware' => [
        'web',
        'auth',
        'role:admin',
    ],
    'extensions' => [],
    'security_strategy' => null,
];
