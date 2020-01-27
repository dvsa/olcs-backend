<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\ContextInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Context\PluginManager
 */
class PluginManagerTest extends MockeryTestCase
{
    /** @var  PluginManager */
    private $sut;

    public function setUp()
    {
        $this->sut = new PluginManager();
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(
            \Zend\ServiceManager\Exception\RuntimeException::class,
            'stdClass should implement: ' . ContextInterface::class
        );

        //  call
        $this->sut->validatePlugin($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(ContextInterface::class);
        // make sure no exception is thrown
        $this->assertNull($this->sut->validatePlugin($plugin));
    }

    public function testInjectAddressFormatter()
    {
        $mockAddressFormatter = m::mock(\Dvsa\Olcs\Api\Service\Helper\FormatAddress::class);

        /** @var ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('AddressFormatter')->andReturn($mockAddressFormatter)
            ->getMock();

        /** @var ServiceLocatorInterface $mockSm */
        $mockSm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('getServiceLocator')->andReturn($mockSl)
            ->getMock();

        /** @var AddressFormatterAwareInterface|m\MockInterface $mockSrv */
        $mockSrv = m::mock(AddressFormatterAwareInterface::class)
            ->shouldReceive('setAddressFormatter')->once()->with($mockAddressFormatter)
            ->getMock();

        //  call
        $actual = $this->sut->injectAddressFormatter($mockSrv, $mockSm);

        static::assertSame($mockSrv, $actual);
    }
}
