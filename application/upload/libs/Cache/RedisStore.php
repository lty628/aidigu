<?php

namespace app\upload\libs\Cache;

use Carbon\Carbon;
use app\upload\libs\Config;
use Predis\Client as RedisClient;

class RedisStore extends AbstractCache
{
    /** @var RedisClient */
    protected $redis;

    /**
     * RedisStore constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = empty($options) ? Config::get('cache.redis') : $options;

        $this->redis = new RedisClient($options);
    }

    /**
     * Get redis.
     *
     * @return RedisClient
     */
    public function getRedis() : RedisClient
    {
        return $this->redis;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, bool $withExpired = false)
    {
        $prefix = $this->getPrefix();

        if (false === strpos($key, $prefix)) {
            $key = $prefix . $key;
        }

        $contents = $this->redis->get($key);
        $contents = json_decode($contents, true);
        return $contents;
        // 取消过期时间 by lee
        // if ($withExpired) {
        //     return $contents;
        // }

        // $isExpired = Carbon::parse($contents['expires_at'])->lt(Carbon::now());

        // return $isExpired ? null : $contents;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value)
    {
        $contents = $this->get($key) ?? [];

        if (\is_array($value)) {
            $contents = $value + $contents;
        } else {
            $contents[] = $value;
        }

        $status = $this->redis->set($this->getPrefix() . $key, json_encode($contents));

        return 'OK' === $status->getPayload();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key) : bool
    {
        $prefix = $this->getPrefix();

        if (false === strpos($key, $prefix)) {
            $key = $prefix . $key;
        }

        return $this->redis->del([$key]) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function keys() : array
    {
        return $this->redis->keys($this->getPrefix() . '*');
    }
}
