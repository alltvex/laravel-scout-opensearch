<?php

return [
    'host' => env('OPENSEARCH_HOST'),
    'user' => env('OPENSEARCH_USER'),
    'password' => env('OPENSEARCH_PASSWORD'),
    'access_key' => env('OPENSEARCH_ACCESS_KEY'),
    'secret' => env('OPENSEARCH_SECRET'),
    'region' => env('OPENSEARCH_REGION'),
    'indices' => [
        'mappings' => [
            'default' => [
                'properties' => [
                    'id' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
        ],
        'settings' => [
            'default' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
        ],
    ],
];
