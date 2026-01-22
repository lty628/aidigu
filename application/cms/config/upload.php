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
    'ext' => 'jpg,gif,md,pdf,jpeg,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z,txt,mp3,mp4,wmv,avi,flv,mkv,css,js,html,htm,xml,json,sql,exe,bat,psd,ai,eps,ps,cdr,svg,ico,tif,tiff,bmp,dng,png',
    // MIME
    'type' => 0,
    // 地址 或 空
    'url' => '',
];