<?php

return [

    'cache' => [
        'redis' => [
            'host' => getenv('REDIS_HOST') !== false ? getenv('REDIS_HOST') : '127.0.0.1',
            'port' => getenv('REDIS_PORT') !== false ? getenv('REDIS_PORT') : '6379',
            'database' => getenv('REDIS_DB') !== false ? getenv('REDIS_DB') : 0,
        ],
    
        /**
         * File cache configs.
         */
        'file' => [
            'dir' => env('ROOT_PATH') . '.cache' . DIRECTORY_SEPARATOR,
            'name' => 'php.server.cache',
        ],
    ],
    'storge' => [
        // 值为类名
        'type' => 's3File', // File S3File ...
        // 在S3File中使用的配置
        'S3FileConfig' => [
            'credentials' => [
                'key'    => 'ASDFASF',
                'secret' => 'ASDFSADFASDFGGAWE2E',
            ],
            'Bucket' => 'test',
            'region' => '中国华北一区',
            'version' => 'latest',
            'endpoint' => 'http://192.168.1.11/',
            'ACL'    => 'public-read',
            'use_path_style_endpoint' => true,
            // 保存路径
            'Path' => '',
        ],
    ],
];
