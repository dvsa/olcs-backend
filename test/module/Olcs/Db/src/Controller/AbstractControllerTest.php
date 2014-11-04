<?php

/**
 * Tests AbstractController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Zend\Http\Response;

/**
 * Tests AbstractController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Create a mock
     *
     * @param array $methods
     */
    protected function getMockController($methods = array())
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMockForAbstractClass(
            '\Olcs\Db\Controller\AbstractController',
            array(),
            '',
            true,
            true,
            true,
            // Mocked methods
            $methods
        );
    }

    /**
     * Test onDispatch
     *  without RouteMatch
     *
     * @expectedException \Zend\Mvc\Exception\DomainException
     *
     * @group Controller
     * @group AbstractController
     */
    public function testOnDispatchWithoutRouteMatch()
    {
        $this->getMockController(array('logRequest'));

        $mockEvent = $this->getMockBuilder('\Zend\Mvc\MvcEvent', array('getRouteMatch'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue(false));

        $this->controller->onDispatch($mockEvent);
    }

    /**
     * Test onDispatch
     *  with RouteMatch
     *  with Missing Action
     *
     * @group Controller
     * @group AbstractController
     */
    public function testOnDispatchWithRouteMatchWithMissingAction()
    {
        $this->getMockController(array('logRequest', 'log', 'notFoundAction'));

        $mockRouteMatch = $this->getMock('\stdClass', array('getParam'));

        $mockRouteMatch->expects($this->any())
            ->method('getParam')
            ->with('action')
            ->will($this->returnValue('missing-action'));

        $mockEvent = $this->getMockBuilder('\Zend\Mvc\MvcEvent', array('getRouteMatch'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($mockRouteMatch));

        // We are expecting this method to be called as the action doesn't exist
        $this->controller->expects($this->once())
            ->method('notFoundAction');

        $this->controller->onDispatch($mockEvent);
    }

    /**
     * Test onDispatch
     *  with RouteMatch
     *  without Action
     *  with method
     *
     * @group Controller
     * @group AbstractController
     */
    public function testOnDispatchWithRouteMatchWithoutActionWithMethod()
    {
        $this->getMockController(array('logRequest', 'log', 'get'));

        $mockRouteMatch = $this->getMock('\stdClass', array('getParam', 'setParam'));

        $mockRouteMatch->expects($this->at(1))
            ->method('getParam')
            ->with('id')
            ->will($this->returnValue(123));

        $mockRequest = $this->getMock('\stdClass', array('getMethod'));

        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $mockEvent = $this->getMockBuilder(
            '\Zend\Mvc\MvcEvent',
            array(
                'getRouteMatch',
                'getRequest'
            )
        )->disableOriginalConstructor()->getMock();

        $mockEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($mockRouteMatch));

        $mockEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($mockRequest));

        // We are expecting this method to be called as the method was get without an id
        $this->controller->expects($this->once())
            ->method('get');

        $this->controller->onDispatch($mockEvent);
    }

    /**
     * Test onDispatch
     *  with RouteMatch
     *  without Action
     *  with method
     *  throwing exception
     *
     * @group Controller
     * @group AbstractController
     */
    public function testOnDispatchWithRouteMatchWithoutActionWithMethodWithException()
    {
        $this->getMockController(array('logRequest', 'log', 'get', 'respond'));

        $mockRouteMatch = $this->getMock('\stdClass', array('getParam', 'setParam'));

        $mockRouteMatch->expects($this->at(0))
            ->method('getParam')
            ->with('action')
            ->will($this->returnValue(false));

        $mockRouteMatch->expects($this->at(1))
            ->method('getParam')
            ->with('id')
            ->will($this->returnValue(123));

        $mockRequest = $this->getMock('\stdClass', array('getMethod'));

        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $mockEvent = $this->getMockBuilder(
            '\Zend\Mvc\MvcEvent',
            array(
                'getRouteMatch',
                'getRequest'
            )
        )->disableOriginalConstructor()->getMock();

        $mockEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($mockRouteMatch));

        $mockEvent->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($mockRequest));

        $this->controller->expects($this->once())
            ->method('get')
            ->will($this->throwException(new \Olcs\Db\Exceptions\RestResponseException('Test', 100)));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(100, 'Test');

        $this->controller->onDispatch($mockEvent);
    }

    /**
     * Test notFoundAction
     *
     * @group Controller
     * @group AbstractController
     */
    public function testNotFoundAction()
    {
        $this->getMockController(array('respond'));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_404);

        $this->controller->notFoundAction();
    }

    /**
     * Test getControllerName
     *
     * @group Controller
     * @group AbstractController
     */
    public function testGetControllerName()
    {
        $paramsMock = $this->getMock('\stdClass', array('fromRoute'));

        $paramsMock->expects($this->once())
            ->method('fromRoute')
            ->with('controller')
            ->will($this->returnValue('SomeController'));

        $this->getMockController(array('params'));

        $this->controller->expects($this->once())
            ->method('params')
            ->will($this->returnValue($paramsMock));

        $this->assertEquals('SomeController', $this->controller->getControllerName());
    }
}
