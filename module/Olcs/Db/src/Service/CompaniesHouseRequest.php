<?php

/**
 * Companies House Request Service
 * Counts requests to companies house API
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Olcs\Db\Service;

/**
 * Companies House Request Service
 */
class CompaniesHouseRequest extends ServiceAbstract
{

    /**
     * Saves request information
     *
     * @param string $requestType
     * @return \Olcs\Db\Entity\CompaniesHouseRequest
     */
    public function initiateRequest($requestType)
    {
        $entityName = $this->getEntityName();
        $entityManager = $this->getEntityManager();

        $companiesHouseRequest = new $entityName();
        $companiesHouseRequest->setRequestType($requestType);

        $serverIpAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $companiesHouseRequest->setIpAddress($serverIpAddress);

        $companiesHouseRequest->setRequestedOn(new \DateTime('NOW'));

        $entityManager->persist($companiesHouseRequest);
        $entityManager->flush();

        return $companiesHouseRequest;
    }
}
