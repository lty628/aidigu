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
        'type' => env('storage.type'), // File S3File ...
        'FileConfig' => [
            'Path' => './uploads/cloud_upload/'
        ],
        // 在S3File中使用的配置
        'S3FileConfig' => [
            'credentials' => [
                'key'    => env('s3Config.awsAccessKey'),
                'secret' => env('s3Config.awsSecretKey'),
            ],
            'Bucket' => env('s3Config.bucket'),
            'region' => env('s3Config.region'),
            'version' => 'latest',
            'endpoint' => env('s3Config.endpoint'),
            'ACL'    => 'public-read',
            'use_path_style_endpoint' => true,
            // 保存路径
            'Path' => '',
        ],
    ],
];