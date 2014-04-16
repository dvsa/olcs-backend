<?php

/**
 * Tests LicenceVehicleController
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\LicenceVehicleController;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Tests LicenceVehicleController
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class LicenceVehicleControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\LicenceVehicleController',
            array(
                'respond',
                'getDataFromQuery',
                'getService'
            )
        );
    }

    /**
     * Test getList
     *  with exception
     *
     * @expectedException \Olcs\Db\Exceptions\RestResponseException
     */
    public function testGetListWithException()
    {
        $options = array(
            'foo' => 'bar'
        );

        $mockLicenceVehicleService = $this->getMock('\stdClass', array('getVehicleList'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockLicenceVehicleService->expects($this->once())
            ->method('getVehicleList')
            ->with($options)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('LicenceVehicle')
            ->will($this->returnValue($mockLicenceVehicleService));

        $this->controller->getList();
    }

    /**
     * Test valid getList
     */
    public function testGetList()
    {
        $options = array(
            'foo' => 'bar'
        );

        $return = array(
            'this' => 'that'
        );

        $mockLicenceVehicleService = $this->getMock('\stdClass', array('getVehicleList'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockLicenceVehicleService->expects($this->once())
            ->method('getVehicleList')
            ->with($options)
            ->will($this->returnValue($return));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('LicenceVehicle')
            ->will($this->returnValue($mockLicenceVehicleService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, 'Results found', $return);

        $this->controller->getList();
        
    }
}
