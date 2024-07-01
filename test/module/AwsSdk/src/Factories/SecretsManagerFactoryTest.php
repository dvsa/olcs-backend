<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Aws\Credentials\CredentialsInterface;
use Aws\SecretsManager\SecretsManagerClient;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManager;
use Dvsa\Olcs\AwsSdk\Factories\SecretsManagerFactory;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerInterface;

class SecretsManagerFactoryTest extends MockeryTestCase
{
    protected $sm;

    protected $sut;

    public function setUp(): void
    {
        $this->sut = new SecretsManagerFactory();
    }

    public function testInvoke(): void
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
        $sm = \Mockery::mock(ContainerInterface::class);
        $sm->shouldReceive('get')->with('config')->andReturn($config);
        $sm->shouldReceive('get')->with('AwsCredentialsProvider')->andReturn($provider);
        $sm->shouldReceive('get')->with(CacheEncryption::class)->andReturn(\Mockery::mock(CacheEncryption::class));
        $sm->shouldReceive('get')->with('SecretsManagerClient')->andReturn(new SecretsManagerClient([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $provider
        ]));
        $secretsManager = $this->sut->__invoke($sm, SecretsManager::class);
        $this->assertInstanceOf(SecretsManager::class, $secretsManager);
    }
}
