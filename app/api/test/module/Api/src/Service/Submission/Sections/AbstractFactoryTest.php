<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory;
use Dvsa\OlcsTest\Api\Service\Submission\Sections\Stub\AbstractSectionStub;
use Interop\Container\ContainerInterface;
use Laminas\View\Renderer\PhpRenderer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\Sections\AbstractFactory
 */
class AbstractFactoryTest extends MockeryTestCase
{
    /** @var  ContainerInterface | m\MockInterface */
    private $mockSl;

    public function setUp(): void
    {
        $this->mockSl = m::mock(ContainerInterface::class);
    }

    public function testInvoke()
    {
        $reqName = AbstractSectionStub::class;

        $this->mockSl
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        'ViewRenderer' => m::mock(PhpRenderer::class),
                        'QueryHandlerManager' => m::mock(QueryHandlerManager::class),
                    ];

                    return $map[$class];
                }
            );

        $actual = (new AbstractFactory())->__invoke($this->mockSl, $reqName);

        static::assertInstanceOf(AbstractSectionStub::class, $actual);
    }
}
