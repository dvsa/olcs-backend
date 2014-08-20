<?php

/**
 * Tests TradingNames
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace OlcsTest\Db\Controller;

use PHPUnit_Framework_TestCase;

/**
 * Tests TradingNames
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class TradingNamesControllerTest extends PHPUnit_Framework_TestCase
{
    private $controller;

    /**
     * Setup the controller
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->controller = $this->getMock(
            '\Olcs\Db\Controller\TradingNamesController',
            array(
                'respond',
                'getDataFromQuery',
                'getService',
                'formatDataFromJson',
            )
        );
    }

    public function testCreateWithResponse()
    {
        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue($this->getMock('\Zend\Http\Response')));

        $this->controller->create([]);
    }

    /**
     * @group current
     */
    public function testCreateWithData()
    {
        $mockService = $this->getMock('\stdClass', array('removeAll', 'create'));
        $this->controller->expects($this->any())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue(array(
                'licence' => 7,
                'tradingNames' => array(
                    array('name' => 'name', 'licence' => 1)
                )
            )));


        $this->controller->create([]);
    }

    /**
     * @group current
     */
    public function testCreateWithExc()
    {
        $mockService = $this->getMock('\stdClass', array('removeAll', 'create'));

        $mockService->expects($this->any())
            ->method('removeAll')
            ->will($this->throwException(new \Exception));


        $this->controller->expects($this->once())
            ->method('getService')
            ->will($this->returnValue($mockService));

        $this->controller->expects($this->once())
            ->method('formatDataFromJson')
            ->will($this->returnValue(array(
                array('name' => 'name', 'licence' => 1)
            )));


        $this->controller->create([]);
    }


}
