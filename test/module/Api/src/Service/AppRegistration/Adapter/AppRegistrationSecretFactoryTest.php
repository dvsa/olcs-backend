<?php

namespace Dvsa\OlcsTest\Api\Service\AppRegistration\Adapter;

use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecretFactory;
use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerInterface;

class AppRegistrationSecretFactoryTest extends MockeryTestCase
{
    /**
     * @dataProvider secretsProviders
     * @throws \Exception
     */
    public function testInvoke(string $secretsProvider, $args): void
    {
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->expects('get')->with('config')->andReturn(['app-registrations' => ['secrets' => ['provider' => $secretsProvider]]]);
        $mockSl->shouldReceive('get')->with($secretsProvider)->andReturn(new $secretsProvider(...$args));

        $this->assertInstanceOf(
            AppRegistrationSecret::class,
            (new AppRegistrationSecretFactory())->__invoke($mockSl, AppRegistrationSecret::class)
        );
    }

    public function secretsProviders(): \Generator
    {
        yield [LocalSecretsManager::class, [[]]];
        yield [SecretsManager::class, [m::mock(SecretsManagerClient::class), m::mock(CacheEncryption::class)]];
    }

    public function testInvokeFallbackToLocal()
    {
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->expects('get')->with('config')->andReturn(['app-registrations' => ['secrets' => ['provider' => ""]]]);
        $mockSl->shouldReceive('get')->with(LocalSecretsManager::class)->andReturn(new LocalSecretsManager([]));
        $this->assertInstanceOf(
            AppRegistrationSecret::class,
            (new AppRegistrationSecretFactory())->__invoke($mockSl, AppRegistrationSecret::class)
        );
    }
}
