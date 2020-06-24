<?php

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Aws\Sqs\SqsClient;
use Dvsa\Olcs\AwsSdk\Factories\SqsClientFactory;
use PHPUnit\Framework\TestCase;
use OlcsTest\Bootstrap;
use Aws\Credentials\CredentialsInterface;

class SqsClientFactoryTest extends TestCase
{
    protected $sm;

    protected $sut;

    public function setUp(): void
    {
        $this->sut = new SqsClientFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    /**
     * @dataProvider dpTestCreateService
     */
    public function testCreateService($sqsOptions)
    {
        // Params
        $config = [
            'companies_house_connection' => ['proxy' => 'proxy-url'],
            'awsOptions' => [
                'region' => 'eu-west-1',
                'version' => 'latest',
                $sqsOptions
            ]
        ];
        $provider = \Mockery::mock(CredentialsInterface::class);
        // Mocks
        $this->sm->setService('AwsCredentialsProvider', $provider);
        $this->sm->setService('Config', $config);


        /**
         * @var SqsClient $sqsClient
         */
        $sqsClient = $this->sut->createService($this->sm);

        $this->assertInstanceOf(SqsClient::class, $sqsClient);
    }

    public function dpTestCreateService()
    {
        return [
            [
                'with_sqs_options' => [
                    'sqsOptions' => [
                        'credentials' => [
                            'key' => 'some_key',
                            'secret' => 'some_secret',
                        ],
                    ]
                ],
                'without-sqs-options' => []
            ]
        ];
    }
}
