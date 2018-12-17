<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\ConvertToPdf\WebServiceClientFactory;
use Dvsa\Olcs\Api\Service\ConvertToPdf\WebServiceClient;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class WebServiceClientFactoryTest
 */
class WebServiceClientFactoryTest extends MockeryTestCase
{
    public function testCreateServiceNoConfig()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn([]);

        $this->expectException(\RuntimeException::class, 'Missing print service config[convert_to_pdf][uri]');
        $sut = new WebServiceClientFactory();
        $sut->createService($mockSl);
    }

    public function testCreateService()
    {
        $config = [
            'convert_to_pdf' => [
                'uri' => 'http://foo.com:90',
            ]
        ];

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn($config);

        $sut = new WebServiceClientFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(WebServiceClient::class, $service);
        $this->assertSame('http://foo.com:90/', $service->getHttpClient()->getUri()->toString());
    }
}
