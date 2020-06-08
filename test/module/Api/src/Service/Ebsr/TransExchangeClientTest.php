<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Validator\Xsd;
use Zend\Http\Client as RestClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TransExchangeClientTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr
 */
class TransExchangeClientTest extends TestCase
{
    /**
     * Tests making a request
     */
    public function testMakeRequest()
    {
        $requestBody = 'body';
        $responseContent = '<success></success>';
        $responseStringContent = 'string<success></success>string';

        $result = [
            'success' => true
        ];

        $domDocument = new \DOMDocument();

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('setContent')->with($requestBody);

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('getContent')->andReturn($responseContent);
        $mockResponse->shouldReceive('toString')->andReturn($responseStringContent);

        $mockClient = m::mock(RestClient::class);
        $mockClient->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockClient->shouldReceive('send')->andReturn($mockResponse);

        $mockParser = m::mock(ParseXmlString::class);
        $mockParser->shouldReceive('filter')->once()->with($responseContent)->andReturn($domDocument);

        $mockXsd = m::mock(Xsd::class);
        $mockXsd->shouldReceive('isValid')->once()->with($domDocument)->andReturn(true);

        $mockFilter = m::mock(MapXmlFile::class);
        $mockFilter->shouldReceive('filter')->once()->with($domDocument)->andReturn($result);

        $sut = new TransExchangeClient($mockClient, $mockFilter, $mockParser, $mockXsd);

        $this->assertEquals($result, $sut->makeRequest($requestBody));
    }

    /**
     * Tests exception thrown when request not valid
     */
    public function testMakeRequestThrowsException()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\TransxchangeException::class);
        $this->expectExceptionMessage('TransXchange response did not validate against the schema: message 1, message 2');

        $requestBody = 'body';
        $responseContent = '<success></success>';
        $responseStringContent = 'string<success></success>string';

        $result = [
            'success' => true
        ];

        $errorMessages = [
            'message 1',
            'message 2'
        ];

        $domDocument = new \DOMDocument();

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('setContent')->with($requestBody);

        $mockResponse = m::mock(Response::class);
        $mockResponse->shouldReceive('getContent')->andReturn($responseContent);
        $mockResponse->shouldReceive('toString')->andReturn($responseStringContent);

        $mockClient = m::mock(RestClient::class);
        $mockClient->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockClient->shouldReceive('send')->andReturn($mockResponse);

        $mockParser = m::mock(ParseXmlString::class);
        $mockParser->shouldReceive('filter')->once()->with($responseContent)->andReturn($domDocument);

        $mockXsd = m::mock(Xsd::class);
        $mockXsd->shouldReceive('isValid')->once()->with($domDocument)->andReturn(false);
        $mockXsd->shouldReceive('getMessages')->once()->andReturn($errorMessages);

        $mockFilter = m::mock(MapXmlFile::class);

        $sut = new TransExchangeClient($mockClient, $mockFilter, $mockParser, $mockXsd);

        $this->assertEquals($result, $sut->makeRequest($requestBody));
    }
}
