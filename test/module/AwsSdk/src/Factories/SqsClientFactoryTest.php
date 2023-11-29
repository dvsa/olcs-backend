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
     * @dataProvider dpTestInvoke
     */
    public function testInvoke($sqsOptions)
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
        $this->sm->setService('Config', $config);


        /**
         * @var SqsClient $sqsClient
         */
        $sqsClient = $this->sut->__invoke($this->sm, SqsClient::class);

        $this->assertInstanceOf(SqsClient::class, $sqsClient);
    }

    public function dpTestInvoke()
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
