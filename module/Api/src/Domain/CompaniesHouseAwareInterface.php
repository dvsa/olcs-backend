<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * CompaniesHouseAwareInterface
 */
interface CompaniesHouseAwareInterface
{
    /**
     * @param \Dvsa\Olcs\Api\Service\CompaniesHouseService $service
     */
    public function setCompaniesHouseService(\Dvsa\Olcs\Api\Service\CompaniesHouseService $service);

    /**
     * @return \Dvsa\Olcs\Api\Service\CompaniesHouseService
     */
    public function getCompaniesHouseService();
}
