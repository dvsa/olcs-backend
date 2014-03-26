<?php

/**
 * Tests Error Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\ErrorController;
use Zend\Http\Response;

/**
 * Tests Error Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ErrorControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\ErrorController',
            array('respond')
        );
    }

    /**
     * Test indexAction
     *  - Should always return 404
     */
    public function testIndexAction()
    {
        $this->controller->expects($this->once())
            ->method('respond')
            ->with(Response::STATUS_CODE_404);

        $this->controller->indexAction();
    }
}
