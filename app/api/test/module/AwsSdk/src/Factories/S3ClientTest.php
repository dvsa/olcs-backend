<?php

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
use Dvsa\Olcs\AwsSdk\S3ClientFactory;
use OlcsTest\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class S3ClientTest
 *
 * @package Dvsa\OlcsTest\AwsSdk
 */
class S3ClientTest extends TestCase
{
    protected $sm;

    protected $sut;

    public function setUp()
    {
        $this->sut = new S3ClientFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'awsOptions' => [
                'region' => 'eu-west-1',
                'version' =>'latest'
            ]
        ];
        $provider = \Mockery::mock(CredentialProvider::class);
        // Mocks
        $this->sm->setService('S3Client', new S3Client([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $provider
        ]));

        $this->sm->shouldRecieve('get')->with('AWSCredentialsProvider')->andReturn($provider);
        $this->sm->shouldRecieve('get')->with('Config')->andReturn($config);
        $s3Client = $this->sut->createService($this->sm);
        $this->assertInstanceOf(S3Client::class, $s3Client);

    }
}