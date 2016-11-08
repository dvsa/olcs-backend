<?php

namespace Dvsa\OlcsTest\Api\Service\Nysiis;

use Dvsa\Olcs\Api\Service\Nysiis\NysiisRestClient;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Zend\Http\Client as RestClient;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class NysiisRestClientTest
 * @package Dvsa\OlcsTest\Api\Service\Nysiis
 */
class NysiisRestClientTest extends MockeryTestCase
{
    /**
 * tests makeRequest
 */
    public function testMakeRequest()
    {
        $volFirstName = 'vol first name';
        $volFamilyName = 'vol family name';
        $nysiisFirstName = 'nysiis first name';
        $nysiisFamilyName = 'nysiis family name';

        $inputJson = '{"volFirstName":"' . $volFirstName . '","volFamilyName":"' . $volFamilyName . '"}';
        $outputJson = '{"nysiisFirstName":"' . $nysiisFirstName . '","nysiisFamilyName":"' . $nysiisFamilyName . '"}';

        $returnedArray = [
            'nysiisFirstName' => $nysiisFirstName,
            'nysiisFamilyName' => $nysiisFamilyName
        ];

        $restResponse = new HttpResponse();
        $restResponse->setStatusCode(200);
        $restResponse->setContent($outputJson);

        $restClient = $this->basicRestClient($inputJson);
        $restClient->shouldReceive('send')->once()->andReturn($restResponse);

        $sut = new NysiisRestClient($restClient);
        $this->assertEquals($returnedArray, $sut->makeRequest($volFirstName, $volFamilyName));
    }

    /**
     * @param $response
     * @param $errorMessage
     *
     * @dataProvider invalidResponseProvider
     */
    public function testMakeRequestInvalidResponse($response, $errorMessage)
    {
        $volFirstName = 'vol first name';
        $volFamilyName = 'vol family name';

        $inputJson = '{"volFirstName":"' . $volFirstName . '","volFamilyName":"' . $volFamilyName . '"}';

        $restClient = $this->basicRestClient($inputJson);
        $restClient->shouldReceive('send')->once()->andReturn($response);

        $this->setExpectedException(NysiisException::class, $errorMessage);

        $sut = new NysiisRestClient($restClient);
        $sut->makeRequest($volFirstName, $volFamilyName);
    }

    /**
     * @return array
     */
    public function invalidResponseProvider()
    {
        $invalidResponse1 = new HttpResponse();
        $invalidResponse1->setStatusCode(400);

        $invalidResponse2 = new HttpResponse();
        $invalidResponse2->setStatusCode(200);
        $invalidResponse2->setContent('{bad json{');

        return [
            [$invalidResponse1, NysiisRestClient::NYSIIS_RESPONSE_INCORRECT],
            [$invalidResponse2, 'Nysiis REST service failure: Decoding failed: Syntax error'],
            [null, NysiisRestClient::NYSIIS_RESPONSE_INCORRECT],
            ['string response', NysiisRestClient::NYSIIS_RESPONSE_INCORRECT]
        ];
    }

    /**
     * @param $inputJson
     * @return m\MockInterface
     */
    public function basicRestClient($inputJson)
    {
        $restClient = m::mock(RestClient::class);
        $restClient->shouldReceive('setEncType')->with('application/json')->once();
        $restClient->shouldReceive('getRequest->setMethod')->with(HttpRequest::METHOD_POST)->once();
        $restClient->shouldReceive('getRequest->setContent')->with($inputJson)->once();

        return $restClient;
    }
}
