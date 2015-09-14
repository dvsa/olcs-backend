<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\CpmsHelperInterface;

/**
 * CpmsAwareTrait
 */
trait CpmsAwareTrait
{
    protected $cpmsService;

    /**
     * @param Dvsa\Olcs\Api\Service\CpmsHelperInterface $service
     */
    public function setCpmsService(CpmsHelperInterface $service)
    {
        $this->cpmsService = $service;
    }

    /**
     * @return Dvsa\Olcs\Api\Service\CpmsHelperInterface
     */
    public function getCpmsService()
    {
        return $this->cpmsService;
    }
}
