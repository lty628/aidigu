<?php

return [
    // 来源域
    'origin' => [
        // 是否开启
        'allow' => true,
        // 允许的域名多个使用数组
        'domain' => '*',
    ],
    // 接收的字段
    'field' => 'file',
    // 上传路径
    'dir' => 'uploads',
    // 上传规则（date、md5、sha1、uniqid）
    'rule' => 'date',
    // 最大字节
    'size' => 0,
    // 后缀
    'ext' => 'jpg,png,gif,md,pdf',
    // MIME
    'type' => 0,
    // 地址 或 空
    'url' => '',
];