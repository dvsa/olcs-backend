<?php

namespace Dvsa\OlcsTest\Api\Service\Nr;

use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Http\Client as RestClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $mockRequest->shouldReceive('setContent')->with($requestBody);
        $mockRequest->shouldReceive('setMethod')->with(Request::METHOD_POST);

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('getStatusCode')->andReturn($statusCode);

        $mockClient = m::mock(RestClient::class);
        $mockClient->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockClient->shouldReceive('send')->andReturn($mockResponse);

        $sut = new InrClient($mockClient);

        $this->assertEquals($statusCode, $sut->makeRequest($requestBody));
    }
}
