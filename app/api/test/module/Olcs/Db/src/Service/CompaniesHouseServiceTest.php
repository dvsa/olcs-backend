<?php

/**
 * Tests Company House Service
 * Tests can occasionally fails because of connection issues. Just run it again.
 * It's better to use live Companies House API than mock it, because in this
 * case we will be aware if third party will change their services.
 * 
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Service;

use OlcsTest\Bootstrap;
use Olcs\Db\Service\CompaniesHouse as CompaniesHouse;
use Zend\ServiceManager\ServiceManager;
use Olcs\Db\Exceptions\RestResponseException;
use PHPUnit_Framework_TestCase;

class CompaniesHouseServiceTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Olcs\Db\Service\CompaniesHouse
     */
    private $service;

    /**
     * Setup the service
     *
     * @return void
     */
    protected function setUp()
    {

        $this->service = $this->getMock('\Olcs\Db\Service\CompaniesHouse', array('getService'));

        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get'));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will(
                $this->returnValue(
                    array(
                        'companies_house_credentials' =>
                            array(
                                'password' => 'XMLGatewayTestPassword',
                                'userId' => 'XMLGatewayTestUserID'
                            )
                    )
                )
            );

        $this->service->setServiceLocator($mockServiceLocator);

        $mockRequestService = $this->getMock(
            '\Olcs\Db\Service\CompaniesHouseRequest',
            array('initiateRequest', 'getId')
        );

        $mockRequestService->expects($this->any())
            ->method('initiateRequest')
            ->will($this->returnValue($mockRequestService));

        $this->service->expects($this->any())
            ->method('getService')
            ->with('CompaniesHouseRequest')
            ->will($this->returnValue($mockRequestService));

    }

    /**
     * Test name search functionality
     *
     * @return void
     */
    public function testNameSearch()
    {
        $result = $this->service->getList(array('type' => 'nameSearch', 'value' => 'N'));
        $this->assertEquals(is_array($result), true);
        $this->assertArrayHasKey('Results', $result);
        $this->assertArrayHasKey('Count', $result);
        $this->assertEquals(count($result['Results']), 10);
        $this->assertEquals($result['Count'], 10);
    }

    /**
     * Test number search functionality
     *
     * @return void
     */
    public function testNumberSearch()
    {
        $result = $this->service->getList(array('type' => 'numberSearch', 'value' => '07425570'));
        $this->assertEquals(is_array($result), true);
        $this->assertArrayHasKey('Results', $result);
        $this->assertArrayHasKey('Count', $result);
        $this->assertEquals(count($result['Results']), 1);
        $this->assertEquals($result['Count'], 1);
    }

    /**
     * Test company details search functionality
     *
     * @return void
     */
    public function testCompanyDetailsSearch()
    {
        $result = $this->service->getList(array('type' => 'companyDetails', 'value' => '07425570'));
        $this->assertEquals(is_array($result), true);
        $this->assertArrayHasKey('Results', $result);
        $this->assertArrayHasKey('Count', $result);
        $this->assertEquals(count($result['Results']), 1);
        $this->assertEquals($result['Count'], 1);
    }

    /**
     * Test number search with not existing company functionality
     *
     * @return void
     */
    public function testNumberNoCompanySearch()
    {
        $result = $this->service->getList(array('type' => 'numberSearch', 'value' => '00000000'));
        $this->assertEquals(is_array($result), true);
        $this->assertArrayHasKey('Results', $result);
        $this->assertArrayHasKey('Count', $result);
        $this->assertEquals(count($result['Results']), 0);
        $this->assertEquals($result['Count'], 0);
    }

    /**
     * Test wrong search method
     *
     * @return void
     * @expectedException Olcs\Db\Exceptions\RestResponseException
     */
    public function testWrongSearch()
    {
        $this->service->getList(array('type' => 'wrongSearch', 'value' => '00000000'));
    }

    /**
     * Test wrong company number search
     *
     * @return void
     * @expectedException Olcs\Db\Exceptions\RestResponseException
     */
    public function testWrongCompanyNumberSearch()
    {
        $this->service->getList(array('type' => 'numberSearh', 'value' => ''));
    }
}
