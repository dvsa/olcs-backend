<?php
namespace Service;

use OlcsCommon\Entity\CompaniesHouseRequest as CompaniesHouseRequest;
use PHPUnit_Framework_TestCase;

class CompaniesHouseRequestServiceTest extends PHPUnit_Framework_TestCase
{
    
    public function testServiceCreated()
    {
        $service = $this->getCompaniesHouseRequestService();
        
        $isCorrectService = $service instanceof \OlcsCommon\Service\CompaniesHouseRequestService;
        $this->assertTrue($isCorrectService);
    }
    
    
    /**
     *
     * Returns a persistent istance of the CompaniesHouseRequest service.
     *
     * @return \OlcsCommon\Service\CompaniesHouseRequestService
     */
    public function getCompaniesHouseRequestService()
    {
        return new \OlcsCommon\Service\CompaniesHouseRequestService;
    }
    
    /**
     *
     * Returns a persistent istance of the CompaniesHouseRequest service.
     *
     * @return \OlcsCommon\Service\CompaniesHouseRequestService
     */
    public function getCompaniesHouseRequestServiceMock($methods = [])
    {
        $mockedClassName = 'CompaniesHouseRequestService_' . rand(0, 1000) . uniqid();

        $service = $this->getMock('\OlcsCommon\Service\CompaniesHouseRequestService', $methods, [], $mockedClassName);

        return $service;
    }
}