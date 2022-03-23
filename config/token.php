<?php


return [
    'payload' => [
        // 签发单位
        "iss" => "http://example.org",
        // 使用单位
        "aud" => "http://example.com",
    ],
    // 设置缓存方式
    'cache' => 'file',
    'key' => '123456',
    // 过期时间（为0永不过期，为零时可setConfig 方法中设置）
    'expire' => '20',
    // 缓存前缀（为空时可setConfig 方法中设置）
    'prefix' => '',
    // 文件缓存配置
    'file' => [
        // 存储类型
        'type' => 'file',
        // 缓存目录
        'path' => '../runtime/cache/token',
    ],
    // redis缓存
    'redis'   =>  [
        // 驱动方式
        'type'   => 'redis',
        // 服务器地址
        'host'   => '127.0.0.1',
    ],
];

