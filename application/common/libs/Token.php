<?php
namespace app\common\libs;

use Firebase\JWT\JWT;
use think\facade\Cache;

// $token = \app\common\libs\Token::setConfig('admin_')->create(['uid' => 1]);
// $isUpdate = \app\common\libs\Token::setConfig('admin_')->update('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwidWlkIjoxLCJpYXQiOjE1ODgwNzAyMjF9.jA97WGyTQYCXk5ytky76cqUlZs12V-kf67_t9uPC_yg');
// $isExpireTime = \app\common\libs\Token::setConfig('admin_')->get('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwidWlkIjoxLCJpYXQiOjE1ODgwNzAyMjF9.jA97WGyTQYCXk5ytky76cqUlZs12V-kf67_t9uPC_yg');
class Token
{
    protected static $config = [];
    protected static $tokenConfig = [];

    // 创建token
    public static function create($payload)
    {
        $time = time();
        $payload = array_merge(self::$tokenConfig['payload'], $payload);
        $payload['iat'] = time();
        $jwt = JWT::encode($payload, self::$config['key']);
        self::set($jwt);
        return $jwt;
    }

    public static function update($token)
    {
        return self::set($token);
    }

    // 添加缓存 token
    public static function set($token)
    {
        return Cache::connect(self::$config['cacheConfig'])->set($token, true);
    }

    public static function get($token)
    {
        if (!self::isExpire($token)) return false;
        return  $jwt = JWT::decode($token, self::$config['key'], array('HS256'));
    }

    // 是否过期
    public static function isExpire($token)
    {
        return Cache::connect(self::$config['cacheConfig'])->get($token);
    }

    // 删除 token
    public static function delete($token)
    {
        return Cache::connect(self::$config['cacheConfig'])->rm($token);
    }

    public static function setConfig($prefix = '', $expire = '9000')
    {
        self::$tokenConfig = config('token.');
        $config['key'] = self::$tokenConfig['key'];
        $cacheType = self::$tokenConfig['cache'];
        $config['cacheConfig'] = self::$tokenConfig[$cacheType];
        $config['cacheConfig']['expire'] = $expire ?: self::$tokenConfig['expire'];
        $config['cacheConfig']['prefix'] = $prefix ?: self::$tokenConfig['prefix'];
        self::$config = $config;
        return new self();
    }

}