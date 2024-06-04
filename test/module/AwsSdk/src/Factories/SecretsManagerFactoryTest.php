<?php

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Aws\Credentials\CredentialsInterface;
use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use Dvsa\Olcs\AwsSdk\Factories\SecretsManagerFactory;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

class SecretsManagerFactoryTest extends TestCase
{
    protected $sm;

    protected $sut;

    public function setUp(): void
    {
        $this->sut = new SecretsManagerFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function testCreateService()
    {
        // Params
        $config = [
            'awsOptions' => [
                'region' => 'eu-west-1',
                'version' => 'latest'
            ]
        ];
        $provider = \Mockery::mock(CredentialsInterface::class);
        // Mocks
        $sm = \Mockery::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);
        $sm->shouldReceive('get')->with('AwsCredentialsProvider')->andReturn($provider);
        $sm->shouldReceive('get')->with(CacheEncryption::class)->andReturn(\Mockery::mock(CacheEncryption::class));
        $sm->shouldReceive('get')->with('SecretsManagerClient')->andReturn(new SecretsManagerClient([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $provider
        ]));
        $secretsManager = $this->sut->createService($sm);
        $this->assertInstanceOf(SecretsManager::class, $secretsManager);
    }
}
