<?php


namespace Dvsa\OlcsTest\Email\Transport;

use Aws\Credentials\CredentialsInterface;
use Aws\S3\S3Client;
use Dvsa\Olcs\Email\Transport\S3FileOptions;
use Dvsa\Olcs\Email\Transport\S3FileOptionsFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * Class S3FileOptionsFactoryTest
 *
 * @package Dvsa\OlcsTest\Email\Transport
 */
class S3FileOptionsFactoryTest extends MockeryTestCase
{

    protected $sm;

    protected $sut;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new S3FileOptionsFactory();
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'awsOptions' => [
                'region' => 'eu-west-1',
                'version' => 'latest',
                's3Options' => [
                    'roleArn' => 'test',
                    'roleSessionName' => 'test'
                ],
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

        $s3Options = $this->sut->createService($this->sm);
        $this->assertInstanceOf(S3FileOptions::class, $s3Options);
    }
}