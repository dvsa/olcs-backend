<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * CompaniesHouseAwareInterface
 */
trait CompaniesHouseAwareTrait
{
    protected $companiesHouseService;

    /**
     * @param \Dvsa\Olcs\Api\Service\CompaniesHouseService $service
     */
    public function setCompaniesHouseService(\Dvsa\Olcs\Api\Service\CompaniesHouseService $service)
    {
        $this->companiesHouseService = $service;
    }

    /**
     * @return \Dvsa\Olcs\Api\Service\CompaniesHouseService
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

    /**
     * Get a list of Current Company Officers from Companies House API
     *
     * @param string $type nameSearch|numberSearch|companyDetails|currentCompanyOfficers
     * @param string $value company number or company name
     *
     * @return array
     */
    public function getCompaniesList($type, $value)
    {
        $data = [
            'type' => $type,
            'value' => $value
        ];

        return $this->getCompaniesHouseService()->getList($data);
    }
}
