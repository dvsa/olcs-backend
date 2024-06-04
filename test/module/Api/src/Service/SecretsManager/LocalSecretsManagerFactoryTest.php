<?php

namespace Dvsa\OlcsTest\Api\Service\SecretsManager;

use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManagerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocalSecretsManagerFactoryTest extends TestCase
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
    public function testCreateService()
    {
        $config = ['localSecretsManager' => ['testSecret' => 'testValue']];
        $sm = \Mockery::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);
        $this->assertInstanceOf(
            \Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager::class,
            $this->sut->createService($sm)
        );
    }
}
