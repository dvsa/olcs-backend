<?php

/**
 * Tests LoggerAwareTrait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Zend\Log\Logger;

/**
 * Tests LoggerAwareTrait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LoggerAwareTraitTest extends PHPUnit_Framework_TestCase
{
    private $trait;

    /**
     * Mock the trait
     */
    public function setUp()
    {
        $this->trait = $this->getMockForTrait(
            '\Olcs\Db\Traits\LoggerAwareTrait',
            array(),
            '',
            true,
            true,
            true,
            // This argument is an array of mocked methods
            array(
                'getServiceLocator'
            )
        );
    }

    /**
     * Test getLogger
     *  without valid logger
     *
     * @expectedException \LogicException
     * @group Traits
     * @group LoggerAwareTrait
     */
    public function testGetLoggerWithoutValidLogger()
    {
        $mockInvalidLogger = $this->getMock('\stdClass');

        $mockServiceLocator = $this->getMock('\stdClass', array('get'));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Logger')
            ->will($this->returnValue($mockInvalidLogger));

        $this->trait->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $this->trait->getLogger();
    }

    /**
     * Test getLogger
     *  with valid logger
     *
     * @group Traits
     * @group LoggerAwareTrait
     */
    public function testGetLoggerWithValidLogger()
    {
        $mockValidLogger = $this->getMock('\Zend\Log\Logger');

        $mockServiceLocator = $this->getMock('\stdClass', array('get'));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Logger')
            ->will($this->returnValue($mockValidLogger));

        $this->trait->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $this->assertEquals($mockValidLogger, $this->trait->getLogger());

        // Second time round should go through the service locator
        $this->assertEquals($mockValidLogger, $this->trait->getLogger());
    }

    /**
     * Test log
     *
     * @dataProvider providerForLog
     * @group Traits
     * @group LoggerAwareTrait
     */
    public function testLog($input, $expected)
    {
        $mockValidLogger = $this->getMock('\Zend\Log\Logger', array('log'));

        $mockServiceLocator = $this->getMock('\stdClass', array('get'));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Logger')
            ->will($this->returnValue($mockValidLogger));

        $this->trait->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $mockValidLogger->expects($this->once())
            ->method('log')
            ->with($expected[0], $expected[1], $expected[2]);

        switch (count($input)) {
            case 1:
                $this->trait->log($input[0]);
                break;
            case 2:
                $this->trait->log($input[0], $input[1]);
                break;
            case 3:
                $this->trait->log($input[0], $input[1], $input[2]);
                break;
        }
    }

    /**
     * Data provider for testLog
     *
     * @return array
     */
    public function providerForLog()
    {
        return array(
            array(
                // Priority defaults to info
                array('Test Message'), array(Logger::INFO, 'Test Message', array()),
                array('Test Message', Logger::ALERT), array(Logger::ALERT, 'Test Message', array()),
                array('Test Message', Logger::ALERT, array('foo' => 'bar')),
                array(Logger::ALERT, 'Test Message', array('foo' => 'bar'))
            )
        );
    }
}
