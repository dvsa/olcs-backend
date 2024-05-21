<?php

namespace Dvsa\OlcsTest\Api\Service\Publication;

use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager as ContextPluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager as ProcessPluginManager;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Service\Publication\PublicationGeneratorFactory;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\PublicationGeneratorFactory
 */
class PublicationGeneratorFactoryTest extends MockeryTestCase
{
    public function testCanCreateServiceWithName()
    {
        /** @var  \Laminas\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(ContainerInterface::class)
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        ContextPluginManager::class => m::mock(ContextPluginManager::class),
                        ProcessPluginManager::class => m::mock(ProcessPluginManager::class),
                        'config' => [
                            'publications' => [],
                        ]
                    ];

                    return $map[$class];
                }
            )
            ->getMock();

        $actual = (new PublicationGeneratorFactory())->__invoke($mockSl, PublicationGenerator::class);

        static::assertInstanceOf(PublicationGenerator::class, $actual);
    }
}
