<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * CpmsAwareTrait
 */
trait CpmsAwareTrait
{
    protected $cpmsService;

    /**
     * @param Dvsa\Olcs\Api\Service\CpmsHelperService $service
     * @note not type-hinted as this will be swapped out
     */
    public function setCpmsService($service)
    {
        $this->cpmsService = $service;
    }

    /**
     * @return Dvsa\Olcs\Api\Service\CpmsHelperService
     */
    public function getCpmsService()
    {
        return $this->cpmsService;
    }
}
