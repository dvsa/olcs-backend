<?php

/**
 * Service Locator Initializer Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue;

use Dvsa\Olcs\Cli\Service\Queue\ServiceLocatorInitializer;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * Service Locator Initializer Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ServiceLocatorInitializerTest extends MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $this->sut = new ServiceLocatorInitializer();
    }

    public function testInvokeWhenInstanceServiceLocatorAware()
    {
        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(ServiceLocatorAwareInterface::class);
        $instance->shouldReceive('setServiceLocator')
            ->with($parentServiceLocator)
            ->once();

        $this->assertSame(
            $instance,
            ($this->sut)($serviceLocator, $instance)
        );
    }

    public function testInvokeWhenInstanceNotServiceLocatorAware()
    {
        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $instance = m::mock(stdClass::class);
        $instance->shouldReceive('setServiceLocator')
            ->never();

        $this->assertSame(
            $instance,
            ($this->sut)($serviceLocator, $instance)
        );
    }

    public function testInitializeWhenInstanceServiceLocatorAware()
    {
        $parentServiceLocator = m::mock(ServiceLocatorInterface::class);

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('getServiceLocator')
            ->withNoArgs()
            ->andReturn($parentServiceLocator);

        $instance = m::mock(ServiceLocatorAwareInterface::class);
        $instance->shouldReceive('setServiceLocator')
            ->with($parentServiceLocator)
            ->once();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $serviceLocator)
        );
    }

    public function testInitializeWhenInstanceNotServiceLocatorAware()
    {
        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $instance = m::mock(stdClass::class);
        $instance->shouldReceive('setServiceLocator')
            ->never();

        $this->assertSame(
            $instance,
            $this->sut->initialize($instance, $serviceLocator)
        );
    }
}
