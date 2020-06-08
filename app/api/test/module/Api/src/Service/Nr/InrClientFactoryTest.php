<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Dvsa\Olcs\Api\Service\Nr\InrClientFactory;
use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\Http\Client as RestClient;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Zend\Http\Client\Adapter\Curl;

/**
 * Class InrClientFactoryTest
 * @package Dvsa\OlcsTest\Api\Service\Nr
 */
class InrClientFactoryTest extends TestCase
{

    public function testCreateServiceNoConfig()
    {
        $this->expectException(\RuntimeException::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new InrClientFactory();
        $sut->createService($mockSl);
    }

    public function testCreateService()
    {
        $config = [
            'nr' => [
                'inr_service' => [
                    'uri' => 'http://testServiceAddress',
                    'adapter' => Curl::class,
                    'options' => []
                ]
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $sut = new InrClientFactory();

        /** @var InrClient $service */
        $service = $sut->createService($mockSl);

        $restClient = $service->getRestClient();
        $wrapper = $restClient->getAdapter();
        $curl = $wrapper->getAdapter();

        $this->assertInstanceOf(InrClientInterface::class, $service);
        $this->assertInstanceOf(RestClient::class, $restClient);
        $this->assertInstanceOf(ClientAdapterLoggingWrapper::class, $wrapper);
        $this->assertInstanceOf(Curl::class, $curl);
    }
}
