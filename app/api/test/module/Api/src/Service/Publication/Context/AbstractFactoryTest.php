<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory;
use Dvsa\OlcsTest\Api\Service\Publication\Context\Stub\AbstractContextStub;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory
 */
class AbstractFactoryTest extends MockeryTestCase
{
    /** @var ServiceLocatorInterface | m\MockInterface */
    private $mockSl;

    /** @var ServiceManager | m\MockInterface */
    private $mockSm;

    public function setUp(): void
    {
        $this->mockSl = m::mock(ServiceLocatorInterface::class);

        $this->mockSm = m::mock(ServiceManager::class)
            ->shouldReceive('getServiceLocator')->andReturn($this->mockSl)
            ->getMock();
    }

    public function testCanCreate()
    {
        $reqName = AbstractContextStub::class;

        static::assertTrue(
            (new AbstractFactory())->canCreate($this->mockSl, $reqName)
        );
    }

    public function testInvoke()
    {
        $this->mockSl
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->once()
            ->andReturn(
                m::mock(QueryHandlerManager::class)
            );

        $reqName = AbstractContextStub::class;

        static::assertInstanceOf(
            AbstractContextStub::class,
            (new AbstractFactory())($this->mockSm, $reqName)
        );
    }

    /**
     * @todo OLCS-28149
     */
    public function testCanCreateServiceWithName()
    {
        $name = 'unit_Name';
        $reqName = AbstractContextStub::class;

        static::assertTrue(
            (new AbstractFactory())->canCreateServiceWithName($this->mockSl, $name, $reqName)
        );
    }

    /**
     * @todo OLCS-28149
     */
    public function testCreateServiceWithName()
    {
        $this->mockSl
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->once()
            ->andReturn(
                m::mock(QueryHandlerManager::class)
            );

        $name = 'unit_Name';
        $reqName = AbstractContextStub::class;

        static::assertInstanceOf(
            AbstractContextStub::class,
            (new AbstractFactory())->createServiceWithName($this->mockSm, $name, $reqName)
        );
    }
}
