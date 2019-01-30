<?php

return [
    'adminEmail' => 'admin@example.com',
    'workers' => [
        'http' => [
            'name' => 'httpWorker',
            'port' => '8801',
            'count' => 4,
        ],
        'text' => [
            'name' => 'textWorker',
            'port' => '8802',
            'count' => 4,
        ],
    ],
    'auth' => [
        'test' => 'test',
    ],
];
