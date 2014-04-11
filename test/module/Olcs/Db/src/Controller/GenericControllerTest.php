<?php

/**
 * Tests AbstractController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\GenericController;

class GenericControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGetDashToCamelCaseFilter()
    {
        $controller = new GenericController();

        $filter = $controller->getDashToCamelCaseFilter();

        $this->assertTrue($filter instanceof \Zend\Filter\Word\DashToCamelCase);
    }

    public function testOnDispatch()
    {
        $mockEvent = $this->getMockBuilder('\Zend\Mvc\MvcEvent', array('getRouteMatch'))->disableOriginalConstructor()->getMock();

        $mockEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue(false));

        $filterMock = $this->getMock('\stdClass', array('filter'));

        $filterMock->expects($this->once())
            ->method('filter')
            ->with('foo-bar')
            ->will($this->returnValue('FooBar'));

        $paramsMock = $this->getMock('\stdClass', array('fromRoute'));

        $paramsMock->expects($this->once())
            ->method('fromRoute')
            ->will($this->returnValue('foo-bar'));

        $controller = $this->getMock('\Olcs\Db\Controller\GenericController', array('getDashToCamelCaseFilter', 'params', 'setServiceName', 'logRequest', 'doDispatch'));

        $controller->expects($this->once())
            ->method('getDashToCamelCaseFilter')
            ->will($this->returnValue($filterMock));

        $controller->expects($this->once())
            ->method('params')
            ->will($this->returnValue($paramsMock));

        $controller->expects($this->once())
            ->method('setServiceName')
            ->with('FooBar');

        $controller->expects($this->once())
            ->method('logRequest')
            ->with($mockEvent);

        $controller->expects($this->once())
            ->method('doDispatch');

        $controller->onDispatch($mockEvent);
    }
}
