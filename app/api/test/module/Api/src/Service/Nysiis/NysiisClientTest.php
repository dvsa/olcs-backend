<?php

namespace Dvsa\OlcsTest\Api\Service\Nysiis;

use Dvsa\Olcs\Api\Service\Nysiis\NysiisClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Soap\Client as SoapClient;

/**
 * Class NysiisClientTest
 * @package Dvsa\OlcsTest\Api\Service\Data
 */
class NysiisClientTest extends MockeryTestCase
{
    /**
     * testGetNysiisSearchKeys
     */
    public function testRetrievingSearchKeys()
    {
        $inputForename = 'input forename';
        $inputFamilyName = 'input family name';

        $nysissForename = 'nysiss forename';
        $nysissFamilyName = 'nysiss family name';

        $nysiisResult = m::mock(\stdClass::class);
        $nysiisResult->shouldReceive('FirstName')->twice()->andReturn($nysissForename);
        $nysiisResult->shouldReceive('FamilyName')->twice()->andReturn($nysissFamilyName);

        $soapClient = $this->soapClient();
        $soapClient->shouldReceive('GetNYSISSSearchKeys')
            ->with(
                [
                    'firstName' => $inputForename,
                    'familyName' => $inputFamilyName
                ]
            )
            ->andReturn(
                $nysiisResult
            );

        $returnedValue = [
            'forename' => $nysissForename,
            'familyName' => $nysissFamilyName
        ];

        $sut = new NysiisClient($soapClient);

        $this->assertEquals($returnedValue, $sut->makeRequest($inputForename, $inputFamilyName));
    }

    /**
     * Tests making a request when a saop fault is returned
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NysiisException
     * @expectedExceptionMessage SOAP Fault connecting to Nysiis service: Soap error message
     */
    public function testMakeRequestWithSoapFault()
    {
        $inputForename = 'input forename';
        $inputFamilyName = 'input family name';

        $inputData = [
            'firstName' => $inputForename,
            'familyName' => $inputFamilyName
        ];

        $soapClient = $this->soapClient();

        $soapClient->shouldReceive('GetNYSISSSearchKeys')
            ->once()
            ->with($inputData)
            ->andThrowExceptions([new \SoapFault('SOAP-ERROR', 'Soap error message')]);

        $sut = new NysiisClient($soapClient);
        $sut->makeRequest($inputForename, $inputFamilyName);
    }

    /**
     * @return m\MockInterface
     */
    private function soapClient()
    {
        $soapClient = m::mock(SoapClient::class);
        $soapClient->shouldReceive('getFunctions')->once();
        $soapClient->shouldReceive('getLastRequestHeaders')->once();
        $soapClient->shouldReceive('getLastRequest')->once();
        $soapClient->shouldReceive('getLastResponseHeaders')->once();
        $soapClient->shouldReceive('getLastResponse')->once();

        return $soapClient;
    }
}
