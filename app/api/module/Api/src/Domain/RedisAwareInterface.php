<?php

namespace Dvsa\Olcs\Api\Domain;

use Zend\Cache\Storage\Adapter\Redis;

/**
 * Redis Aware Interface
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
interface RedisAwareInterface
{
    /**
     * @param Redis $redis
     *
     * @return void
     */
    public function setRedis(Redis $redis): void;

    /**
     * @return Redis
     */
    public function getRedis(): Redis;
}
