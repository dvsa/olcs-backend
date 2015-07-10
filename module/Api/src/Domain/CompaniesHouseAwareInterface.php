<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * CompaniesHouseAwareInterface
 */
interface CompaniesHouseAwareInterface
{
    /**
     * @param \Olcs\Db\Service\CompaniesHouse $service
     */
    public function setCompaniesHouseService(\Olcs\Db\Service\CompaniesHouse $service);

    /**
     * @return \Olcs\Db\Service\CompaniesHouse
     */
    public function getCompaniesHouseService();
}
