<?php

/**
 * Tests Payment Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Controller\PaymentController;

/**
 * Tests Payment Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PaymentControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = new PaymentController();
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
