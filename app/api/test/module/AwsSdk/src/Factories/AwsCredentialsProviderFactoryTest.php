<?php

namespace Dvsa\OlcsTest\AwsSdk\Factories;

use Dvsa\Olcs\AwsSdk\Factories\AwsCredentialsProviderFactory;
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

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new AwsCredentialsProviderFactory();
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
        $this->sm->setService('Config', $config);

        $actual = $this->sut->createService($this->sm);

        $this->assertIsCallable($actual);
    }
}
