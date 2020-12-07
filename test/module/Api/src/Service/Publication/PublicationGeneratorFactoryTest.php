<?php

namespace Dvsa\OlcsTest\Api\Service\Publication;

use Dvsa\Olcs\Api\Service\Publication\Context\PluginManager as ContextPluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager as ProcessPluginManager;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Service\Publication\PublicationGeneratorFactory;
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
        $mockSl = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->andReturnUsing(
                function ($class) {
                    $map = [
                        ContextPluginManager::class => m::mock(ContextPluginManager::class),
                        ProcessPluginManager::class => m::mock(ProcessPluginManager::class),
                        'Config' => [
                            'publications' => [],
                        ]
                    ];

                    return $map[$class];
                }
            )
            ->getMock();

        $actual = (new PublicationGeneratorFactory())->createService($mockSl);

        static::assertInstanceOf(PublicationGenerator::class, $actual);
    }
}
