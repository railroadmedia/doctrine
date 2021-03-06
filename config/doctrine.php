<?php

return [
    'redis_host' => 'redis',
    'redis_port' => 6379,

    'database_name' => 'tests_doctrine',
    'database_user' => 'root',
    'database_password' => 'root',
    'database_host' => 'mysql',
    'database_driver' => 'pdo_mysql',
    'database_in_memory' => false,

    'entities' => [
        [
            'path' => __DIR__ . '/../src/Entities',
            'namespace' => 'Railroad\Doctrine\Entities'
        ]
    ],
];
