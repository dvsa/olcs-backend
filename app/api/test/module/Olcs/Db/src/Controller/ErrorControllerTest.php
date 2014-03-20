<?php

/**
 * Tests Error Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\ErrorController;

/**
 * Tests Error Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ErrorControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = new ErrorController();
    }

    /**
     * Test respond
     *
     * @dataProvider responseDataProvider
     */
    public function testRespond($code, $summary, $data)
    {
        $response = $this->controller->respond($code, $summary, $data);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals($code, $responseData['Response']['Code']);
        $this->assertEquals($summary, $responseData['Response']['Summary']);
        $this->assertEquals($data, $responseData['Response']['Data']);
    }

    /**
     * Test indexAction
     *  - Should always return 404
     */
    public function testIndexAction()
    {
        $response = $this->controller->indexAction();

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(404, $responseData['Response']['Code']);

        $this->assertTrue($response->isNotFound());
    }

    /**
     * Data provider for respond
     */
    public function responseDataProvider()
    {
        return array(
            array(200, 'OK', array('foo' => 'bar')),
            array(201, 'Created', array('foo' => 'bar')),
            array(204, 'Deleted', null),
            array(404, 'Not found', null)
        );
    }
}
