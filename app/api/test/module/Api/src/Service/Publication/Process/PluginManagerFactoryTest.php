<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Service\Publication\Process\PluginManager;
use Dvsa\Olcs\Api\Service\Publication\Process\PluginManagerFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Api\Service\Publication\Process\PluginManagerFactory
 */
class PluginManagerFactoryTest extends MockeryTestCase
{
    public function testCanCreateServiceWithName()
    {
        /** @var  \Zend\ServiceManager\ServiceLocatorInterface $mockSl */
        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn(
                [
                    'publication_process' => [],
                ]
            )
            ->getMock();

        $actual = (new PluginManagerFactory())->createService($mockSl);

        static::assertInstanceOf(PluginManager::class, $actual);
    }
}
