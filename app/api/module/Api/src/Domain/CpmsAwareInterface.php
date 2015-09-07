<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * Cpms Aware Interface
 */
interface CpmsAwareInterface
{
    /**
     * @param Dvsa\Olcs\Api\Service\CpmsHelperService $service
     * @note not type-hinted as this will be swapped out
     */
    public function setCpmsService($service);

    /**
     * @return Dvsa\Olcs\Api\Service\CpmsHelperService
     */
    public function getCpmsService();
}
