<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\CpmsHelperInterface;

/**
 * Cpms Aware Interface
 */
interface CpmsAwareInterface
{
    /**
     * @param Dvsa\Olcs\Api\Service\CpmsHelperInterface $service
     */
    public function setCpmsService(CpmsHelperInterface $service);

    /**
     * @return Dvsa\Olcs\Api\Service\CpmsHelperInterface
     */
    public function getCpmsService();
}
