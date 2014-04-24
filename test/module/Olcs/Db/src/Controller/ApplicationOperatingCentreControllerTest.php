<?php

/**
 * Tests ApplicationOperatingCentreControllerTest
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Zend\Http\Response;

/**
 * Tests ApplicationOperatingCentreController
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class ApplicationOperatingCentreControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\ApplicationOperatingCentreController',
            array(
                'respond',
                'getDataFromQuery',
                'getService'
            )
        );
    }

    /**
     * Test getList
     */
    public function testGetList()
    {
        $options = array(
            'applicationId' => '10'
        );

        $return = array(
            'this' => 'that'
        );

        $mockApplicationOperatingCentreService = $this->getMock('\stdClass', array('getByApplicationId'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('ApplicationOperatingCentre')
            ->will($this->returnValue($mockApplicationOperatingCentreService));

        $mockApplicationOperatingCentreService->expects($this->once())
            ->method('getByApplicationId')
            ->with($options)
            ->will($this->returnValue($return));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, 'Entity found', $return);

        $this->controller->getList();
    }


    /**
     * Test getList Exception
     */
    public function testGetListWithException()
    {
        $options = array(
        );

        $return = array(
            'this' => 'that'
        );

        $mockApplicationOperatingCentreService = $this->getMock('\stdClass', array('getByApplicationId'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('ApplicationOperatingCentre')
            ->will($this->returnValue($mockApplicationOperatingCentreService));

        $mockApplicationOperatingCentreService->expects($this->once())
            ->method('getByApplicationId')
            ->with($options)
            ->will($this->throwException(new \Exception));

        $this->controller->getList();
    }

}
