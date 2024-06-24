<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\SecretsManager;

use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManagerFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocalSecretsManagerFactoryTest extends MockeryTestCase
{
    private LocalSecretsManagerFactory $sut;

    public function setUp(): void
    {
        $this->sut = new LocalSecretsManagerFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvoke(): void
    {
        $config = ['localSecretsManager' => ['testSecret' => 'testValue']];
        $sm = \Mockery::mock(ContainerInterface::class);
        $sm->shouldReceive('get')->with('config')->andReturn($config);
        $this->assertInstanceOf(
            LocalSecretsManager::class,
            $this->sut->__invoke($sm, LocalSecretsManager::class)
        );
    }
}
