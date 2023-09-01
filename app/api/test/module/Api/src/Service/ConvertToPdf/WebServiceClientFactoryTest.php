<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\ConvertToPdf\WebServiceClientFactory;
use Dvsa\Olcs\Api\Service\ConvertToPdf\WebServiceClient;
use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class WebServiceClientFactoryTest extends MockeryTestCase
{
    public function testInvokeMissingConfig()
    {
        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing print service config[convert_to_pdf][uri]');

        $sut = new WebServiceClientFactory();
        $sut->__invoke($mockSl, WebServiceClient::class);
    }

    public function testInvoke()
    {
        $config = [
            'convert_to_pdf' => [
                'uri' => 'http://foo.com:90',
            ]
        ];

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->andReturn($config);

        $sut = new WebServiceClientFactory();
        $service = $sut->__invoke($mockSl, WebServiceClient::class);

        $this->assertInstanceOf(WebServiceClient::class, $service);
        $this->assertSame('http://foo.com:90/', $service->getHttpClient()->getUri()->toString());
    }
}
