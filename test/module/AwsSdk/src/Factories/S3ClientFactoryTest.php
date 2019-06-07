<?php

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Aws\Credentials\CredentialsInterface;
use Aws\S3\S3Client;
use Dvsa\Olcs\AwsSdk\Factories\S3ClientFactory;
use OlcsTest\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class S3ClientTest
 *
 * @package Dvsa\OlcsTest\AwsSdk
 */
class S3ClientFactoryTest extends TestCase
{
    protected $sm;

    protected $sut;

    public function setUp()
    {
        $this->sut = new S3ClientFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    /**
     *
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
        $this->sm->setService('AWSCredentialsProvider', $provider);
        $this->sm->setService('Config', $config);
        $this->sm->setService('S3Client', new S3Client([
            'region' => $config['awsOptions']['region'],
            'version' => $config['awsOptions']['version'],
            'credentials' => $provider
        ]));
        /**
         * @var S3Client
         */
        $s3Options = $this->sut->createService($this->sm);
        $this->assertInstanceOf(S3Client::class, $s3Options);
    }
}
