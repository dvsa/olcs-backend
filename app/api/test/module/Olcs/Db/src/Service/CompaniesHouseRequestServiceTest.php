<?php

/**
 * Tests Company House Request Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Service;

use OlcsTest\Bootstrap;
use Olcs\Db\Service\CompaniesHouseRequest as CompaniesHouseRequest;
use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase;

class CompaniesHouseRequestServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Olcs\Db\Service\CompaniesHouseRequest
     */
    private $service;

    /**
     * Setup the service
     *
     * @return void
     */
    protected function setUp()
    {

        $serviceManager = Bootstrap::getServiceManager();
        $this->service = new CompaniesHouseRequest();
        $this->service->setServiceLocator($serviceManager);
        $mockEntityManager = $this->getMockBuilder(
            '\Doctrine\ORM\EntityManager',
            array('getConnection')
        )
        ->disableOriginalConstructor()->getMock();

        $this->service->setEntityManager($mockEntityManager);

    }

    /**
     * Test initiateRequest method
     *
     * @return void
     */
    public function testInitiateRequest()
    {
        $request = $this->service->initiateRequest('numberSearch');
        $requestCreated = $request instanceof \OlcsEntities\Entity\CompaniesHouseRequest;
        $this->assertTrue($requestCreated);
    }
}
