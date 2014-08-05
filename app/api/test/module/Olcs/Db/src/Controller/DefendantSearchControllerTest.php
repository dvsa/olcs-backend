<?php

/**
 * Tests DefendantSearchController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\DefendantSearchController;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Tests PersonSearchController
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class DefendantSearchControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\DefendantSearchController',
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

        $mockService = $this->getMock('\stdClass', array('findPersons'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockService->expects($this->once())
            ->method('findPersons')
            ->with($options)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('Person')
            ->will($this->returnValue($mockService));

        $this->controller->getList();
    }

    /**
     * Test getList
     */
    public function testGetList()
    {
        $options = array(
            'foo' => 'bar'
        );

        $return = array(
            'this' => 'that'
        );

        $result = array(
            'Type' => 'results',
            'Results' => $return
        );

        $mockService = $this->getMock('\stdClass', array('findPersons'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockService->expects($this->once())
            ->method('findPersons')
            ->with($options)
            ->will($this->returnValue($return));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('Person')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, '', $result);

        $this->controller->getList();
    }
}
