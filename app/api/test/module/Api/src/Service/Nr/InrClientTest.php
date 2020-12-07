<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Http\Client as RestClient;
use Laminas\Http\Request;
use Laminas\Http\Response;

/**
 * Class InrClientTest
 * @package Dvsa\OlcsTest\Api\Service\Nr
 */
class InrClientTest extends MockeryTestCase
{
    /**
     * Tests makeRequest
     */
    public function testMakeRequest()
    {
        $statusCode = 202;
        $requestBody = 'xml';

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('setContent')->once()->with($requestBody);
        $mockRequest->shouldReceive('setMethod')->once()->with(Request::METHOD_POST);
        $mockRequest->shouldReceive('toString')->once();

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('getStatusCode')->andReturn($statusCode);
        $mockResponse->shouldReceive('toString')->once();

        $mockClient = m::mock(RestClient::class);
        $mockClient->shouldReceive('setEncType')->once()->with('text/xml');
        $mockClient->shouldReceive('getRequest')->times(3)->andReturn($mockRequest);
        $mockClient->shouldReceive('send')->once()->andReturn($mockResponse);

        $sut = new InrClient($mockClient);

        $this->assertEquals($statusCode, $sut->makeRequest($requestBody));
    }

    public function testClose()
    {
        $mockRestClient = m::mock(RestClient::class);
        $mockRestClient->shouldReceive('getAdapter->close')->once()->withNoArgs();

        $sut = new InrClient($mockRestClient);
        $sut->close();
    }

    public function testGetRestClient()
    {
        $mockRestClient = m::mock(RestClient::class);
        $sut = new InrClient($mockRestClient);
        $this->assertEquals($mockRestClient, $sut->getRestClient());
    }
}
