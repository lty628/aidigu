<?php
namespace TusPhpS3\Cache;

use TusPhp\Cache\RedisStore;

use Predis\ClientInterface;

class PredisCache
extends RedisStore
{
    const DEFAULT_EXPIRE = 86400*3; // 3 days

    protected $expire;

    /**
     * RedisStore constructor.
     *
     * @param array $options
     */
    public function __construct(ClientInterface $redis, int $expire = self::DEFAULT_EXPIRE, string $prefix = null)
    {
        $this->redis = $redis;
        $this->setExpire($expire);

        if($prefix)
            $this->setPrefix($prefix);
    }

    protected function setExpire(int $expire) : self
    {
        $this->expire = $expire;

        return $this;
    }

    protected function getExpire() : int
    {
        return $this->expire;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value)
    {
        if(parent::set($key, $value))
        {
            $this->redis->expire($this->getPrefix() . $key, $this->getExpire());

            return true;
        }

        return false;
    }
}
