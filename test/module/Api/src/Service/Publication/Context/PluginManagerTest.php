<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\ContextInterface;
use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ConfigInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Context\PluginManager
 */
class PluginManagerTest extends MockeryTestCase
{
    /** @var  PluginManager */
    private $sut;

    public function setUp()
    {
        /** @var  \Zend\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn(
                [
                    'publication_context' => [],
                ]
            )
            ->getMock();

        $this->sut = new PluginManager($mockSl);
    }

    public function testValidatePluginFail()
    {
        $invalidPlugin = new \stdClass();

        //  expect
        $this->expectException(\Zend\ServiceManager\Exception\InvalidServiceException::class);

        //  call
        $this->sut->validate($invalidPlugin);
    }

    public function testValidatePluginOk()
    {
        $plugin = m::mock(ContextInterface::class);
        $this->sut->validate($plugin);
    }

    public function testInjectAddressFormatter()
    {
        $mockAddressFormatter = m::mock(\Dvsa\Olcs\Api\Service\Helper\FormatAddress::class);

        /** @var \Zend\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('AddressFormatter')->andReturn($mockAddressFormatter)
            ->getMock();

        /** @var \Zend\ServiceManager\ServiceManager $mockSm */
        $mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class)
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
