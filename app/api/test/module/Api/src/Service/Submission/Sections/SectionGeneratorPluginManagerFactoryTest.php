<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager;
use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManagerFactory;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManagerFactory
 */
class SectionGeneratorPluginManagerFactoryTest extends MockeryTestCase
{
    public function testCanCreateServiceWithName()
    {
        /** @var  \Laminas\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(ContainerInterface::class)
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn(
                [
                    'submissions' => [
                        'sections' => [],
                    ],
                ]
            )
            ->getMock();

        $actual = (new SectionGeneratorPluginManagerFactory())->__invoke($mockSl, SectionGeneratorPluginManager::class);

        static::assertInstanceOf(SectionGeneratorPluginManager::class, $actual);
    }
}
