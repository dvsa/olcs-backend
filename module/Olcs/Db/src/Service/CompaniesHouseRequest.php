<?php

/**
 * Companies House Request Service
 * Counts requests to companies house API 
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace OlcsCommon\Service;

/**
 * Companies House Request Service
 */
class CompaniesHouseRequest  extends ServiceAbstract
{
    
    /**
     * Saves request information
     * @param string $requestType
     *
     * @return \OlcsEntities\Entity\CompaniesHouseRequest
     */
    public function initiateRequest($requestType)
    {

        $entityName = $this->getEntityName();
        $entityManager = $this->getEntityManager();
        
        $companiesHouseRequest = new $entityName();
        $companiesHouseRequest->setRequestType($requestType);
        $companiesHouseRequest->setIpAddress($_SERVER['REMOTE_ADDR']);
      
        $companiesHouseRequest->setRequestedOn(new \DateTime('NOW'));
        
        $entityManager->persist($companiesHouseRequest);
        $entityManager->flush();

        return $companiesHouseRequest;
        
    }
    
}
