<?php

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Dvsa\Olcs\AwsSdk\Factories\AwsCredentialsProviderFactory;
use Mockery as m;
use OlcsTest\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class AwsCredentialsFactoryTest
 *
 * @package Dvsa\OlcsTest\AwsSdk\Factories
 */
class AwsCredentialsProviderFactoryTest extends TestCase
{

    protected $sm;

    protected $sut;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new AwsCredentialsProviderFactory();
    }

    public function testCreateServiceWithInstanceProfileCredentials()
    {
        // Params
        $config = [
            'awsOptions' => [
                'useEnvCredentials' => false,
                'region' => 'eu-west-1',
                'version' => 'latest',
                's3Options' => [
                    'roleArn' => 'test',
                    'roleSessionName' => 'test'
                ],
            ]
        ];
        $this->sm->setService('Config', $config);

        $sut = m::mock(AwsCredentialsProviderFactory::class)->makePartial();
        $sut->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('getInstanceProfileCredentialProvider')
            ->once()
            ->withNoArgs()
            ->andReturn(
                function () {
                }
            );

        $actual = $this->sut->createService($this->sm);
        $this->assertIsCallable($actual);
    }

    public function testCreateServiceWithEnvCredentials()
    {
        // Params
        $config = [
            'awsOptions' => [
                'useEnvCredentials' => true,
                'region' => 'eu-west-1',
                'version' => 'latest',
                's3Options' => [
                    'roleArn' => 'test',
                    'roleSessionName' => 'test'
                ],
            ]
        ];
        $this->sm->setService('Config', $config);

        $sut = m::mock(AwsCredentialsProviderFactory::class)->makePartial();
        $sut->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('getEnvCredentialProvider')
            ->once()
            ->withNoArgs()
            ->andReturn(
                function () {
                }
            );

        $actual = $this->sut->createService($this->sm);
        $this->assertIsCallable($actual);
    }
}
