<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory;
use Dvsa\OlcsTest\Api\Service\Publication\Context\Stub\AbstractContextStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory
 */
class AbstractFactoryTest extends MockeryTestCase
{
    /** @var  \Laminas\ServiceManager\ServiceLocatorInterface | m\MockInterface */
    private $mockSl;
    /** @var  \Laminas\ServiceManager\ServiceManager | m\MockInterface */
    private $mockSm;

    public function setUp(): void
    {
        $this->mockSl = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);

        $this->mockSm = m::mock(\Laminas\ServiceManager\ServiceManager::class)
            ->shouldReceive('getServiceLocator')->andReturn($this->mockSl)
            ->getMock();
    }

    public function testCanCreateServiceWithName()
    {
        $name = 'unit_Name';
        $reqName = AbstractContextStub::class;

        static::assertTrue(
            (new AbstractFactory())->canCreateServiceWithName($this->mockSl, $name, $reqName)
        );
    }

    public function testCreateServiceWithName()
    {
        $this->mockSl
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn(
                m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class)
            );

        $name = 'unit_Name';
        $reqName = AbstractContextStub::class;

        static::assertInstanceOf(
            AbstractContextStub::class,
            (new AbstractFactory())->createServiceWithName($this->mockSm, $name, $reqName)
        );
    }
}
