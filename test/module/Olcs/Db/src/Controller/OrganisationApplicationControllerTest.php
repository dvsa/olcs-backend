<?php

/**
 * Tests PersonSearchController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\PersonSearchController;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Tests PersonSearchController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationApplicationControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\OrganisationApplicationController',
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
            'organisation' => 104,
        );

        $mockService = $this->getMock('\stdClass', array('getApplicationsList'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockService->expects($this->once())
            ->method('getApplicationsList')
            ->with($options)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('Organisation')
            ->will($this->returnValue($mockService));

        $this->controller->getList();
    }

    /**
     * Test getList
     *  with exception
     *
     * @expectedException \Olcs\Db\Exceptions\RestResponseException
     */
    public function testGetListWithNoOperatorId()
    {
        $mockService = $this->getMock('\stdClass', array('getApplicationsList'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue(array()));

        $this->controller->getList();
    }

    /**
     * Test getList
     */
    public function testGetList()
    {
        $options = array(
            'organisation' => 104,
        );

        $return = array(
            'this' => 'that'
        );

        $result = array(
            'Count' => count($return),
            'Results' => $return,
        );

        $mockLicenceService = $this->getMock('\stdClass', array('getApplicationsList'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockLicenceService->expects($this->once())
            ->method('getApplicationsList')
            ->with($options)
            ->will($this->returnValue($result));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('Organisation')
            ->will($this->returnValue($mockLicenceService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, '', $result);

        $this->controller->getList();
    }
}
