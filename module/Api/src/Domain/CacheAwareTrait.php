<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;

/**
 * Cache Aware Trait
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
trait CacheAwareTrait
{
    /** @var CacheEncryption */
    protected $cacheService;

    /**
     * @param CacheEncryption $cacheService
     *
     * @return void
     */
    public function setCache(CacheEncryption $cacheService): void
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @return CacheEncryption
     */
    public function getCache(): CacheEncryption
    {
        return $this->cacheService;
    }
}
