<?php

/**
 * Address Formatter Initializer Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;
use Dvsa\Olcs\Api\Service\Publication\Context\AddressFormatterInitializer;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * Address Formatter Initializer Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AddressFormatterInitializerTest extends MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $this->sut = new AddressFormatterInitializer();
    }

    public function testInvokeWhenInstanceAddressFormatterAware()
    {
        $formatAddress = m::mock(FormatAddress::class);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('AddressFormatter')
            ->andReturn($formatAddress);

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(AddressFormatterAwareInterface::class);
        $instance->shouldReceive('setAddressFormatter')
            ->with($formatAddress)
            ->once();

        $this->assertSame(
            $instance,
            ($this->sut)($serviceLocator, $instance)
        );
    }

    public function testInvokeWhenInstanceNotAddressFormatterAware()
    {
        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $instance = m::mock(stdClass::class);
        $instance->shouldReceive('setAddressFormatter')
            ->never();

        $this->assertSame(
            $instance,
            ($this->sut)($serviceLocator, $instance)
        );
    }

    public function testInitializeWhenInstanceAddressFormatterAware()
    {
        $formatAddress = m::mock(FormatAddress::class);

        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);
        $parentServiceLocator->shouldReceive('get')
            ->with('AddressFormatter')
            ->andReturn($formatAddress);

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(AddressFormatterAwareInterface::class);
        $instance->shouldReceive('setAddressFormatter')
            ->with($formatAddress)
            ->once();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $serviceLocator)
        );
    }

    public function testInitializeWhenInstanceNotAddressFormatterAware()
    {
        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $instance = m::mock(stdClass::class);
        $instance->shouldReceive('setAddressFormatter')
            ->never();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $serviceLocator)
        );
    }
}
