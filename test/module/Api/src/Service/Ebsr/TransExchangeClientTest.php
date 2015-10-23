<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\Http\Client as RestClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientTest
 * @package OlcsTest\Ebsr\Service
 */
class TransExchangeClientTest extends TestCase
{
    public function testMakeRequest()
    {
        $requestBody = 'body';

        $result = [
            'success' => true
        ];

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('setContent')->with($requestBody);

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('getContent')->andReturn('<success></success>');

        $mockClient = m::mock(RestClient::class);
        $mockClient->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockClient->shouldReceive('send')->andReturn($mockResponse);

        $mockFilter = m::mock(MapXmlFile::class);
        $mockFilter->shouldReceive('filter')->with(m::type('DOMDocument'))->andReturn($result);

        $sut = new TransExchangeClient($mockClient, $mockFilter);

        $this->assertEquals($result, $sut->makeRequest($requestBody));
    }
}
