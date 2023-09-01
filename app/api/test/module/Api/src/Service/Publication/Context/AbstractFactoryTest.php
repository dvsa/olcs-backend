<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory;
use Dvsa\OlcsTest\Api\Service\Publication\Context\Stub\AbstractContextStub;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory
 */
class AbstractFactoryTest extends MockeryTestCase
{
    /** @var ContainerInterface| m\MockInterface */
    private $mockSl;

    /** @var ServiceManager | m\MockInterface */
    private $mockSm;

    public function setUp(): void
    {
        $this->mockSl = m::mock(ContainerInterface::class);

        $this->mockSm = m::mock(ServiceManager::class)
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
}
