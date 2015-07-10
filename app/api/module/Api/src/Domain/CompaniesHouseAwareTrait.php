<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * CompaniesHouseAwareInterface
 */
trait CompaniesHouseAwareTrait
{
    protected $companiesHouseService;

    /**
     * @param \Olcs\Db\Service\CompaniesHouse $service
     */
    public function setCompaniesHouseService(\Olcs\Db\Service\CompaniesHouse $service)
    {
        $this->companiesHouseService = $service;
    }

    /**
     * @return \Olcs\Db\Service\CompaniesHouse
     */
    public function getCompaniesHouseService()
    {
        return $this->companiesHouseService;
    }

    /**
     * Get a list of Current Company Officers from Companies House API
     *
     * @param string $companyNumber 8 digit company number
     *
     * @return array
     */
    public function getCurrentCompanyOfficers($companyNumber)
    {
        $data = [
            'type' => 'currentCompanyOfficers',
            'value' => $companyNumber,
        ];

        $response = $this->getCompaniesHouseService()->getList($data);

        return $response['Results'];
    }
}
