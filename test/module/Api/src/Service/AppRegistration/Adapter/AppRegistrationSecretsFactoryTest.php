<?php

namespace Dvsa\OlcsTest\Api\Service\AppRegistration\Adapter;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecretsFactory;
use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Driver\WrongXdebugVersionException;

class AppRegistrationSecretsFactoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider secretsProviders
     * @throws \Exception
     */
    public function testCreateService(string $secretsProvider, $args): void
    {
        $mockSl = \Mockery::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn(['app-registrations' => ['secrets' => ['provider' => $secretsProvider]]]);
        $mockSl->shouldReceive('get')->with($secretsProvider)->andReturn(new $secretsProvider(...$args));

        $this->assertInstanceOf(
            \Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret::class,
            (new AppRegistrationSecretsFactory())->createService($mockSl)
        );
    }

    public function secretsProviders(): \Generator
    {
        yield [LocalSecretsManager::class, [[]]];
        yield [SecretsManager::class, [\Mockery::mock(\Aws\SecretsManager\SecretsManagerClient::class), \Mockery::mock(\Dvsa\Olcs\Transfer\Service\CacheEncryption::class)]];
    }

    public function testCreateServicefallbackToLocal()
    {
        $mockSl = \Mockery::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn(['app-registrations' => ['secrets' => ['provider' => ""]]]);
        $mockSl->shouldReceive('get')->with(LocalSecretsManager::class)->andReturn(new LocalSecretsManager([]));
        $this->assertInstanceOf(
            \Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret::class,
            (new AppRegistrationSecretsFactory())->createService($mockSl)
        );
    }
}
