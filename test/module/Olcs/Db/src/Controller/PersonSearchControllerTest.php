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
class PersonSearchControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\PersonSearchController',
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

        $mockLicenceService = $this->getMock('\stdClass', array('findAllPersons'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockLicenceService->expects($this->once())
            ->method('findAllPersons')
            ->with($options)
            ->will($this->throwException(new \Exception));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('Licence')
            ->will($this->returnValue($mockLicenceService));

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

        $mockLicenceService = $this->getMock('\stdClass', array('findAllPersons'));

        $this->controller->expects($this->once())
            ->method('getDataFromQuery')
            ->will($this->returnValue($options));

        $mockLicenceService->expects($this->once())
            ->method('findAllPersons')
            ->with($options)
            ->will($this->returnValue($return));

        $this->controller->expects($this->once())
            ->method('getService')
            ->with('Licence')
            ->will($this->returnValue($mockLicenceService));

        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_200, '', $result);

        $this->controller->getList();
    }
}
