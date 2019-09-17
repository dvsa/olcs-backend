<?php

namespace Dvsa\Olcs\Api\Domain;

use Zend\Cache\Storage\Adapter\Redis;

/**
 * Redis Aware Trait
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
trait RedisAwareTrait
{
    /** @var Redis */
    protected $redis;

    /**
     * @param Redis $redis
     *
     * @return void
     */
    public function setRedis(Redis $redis): void
    {
        $this->redis = $redis;
    }

    /**
     * @return Redis
     */
    public function getRedis(): Redis
    {
        return $this->redis;
    }
}
