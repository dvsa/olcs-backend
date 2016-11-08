<?php

namespace Dvsa\OlcsTest\Api\Service\Nysiis;

use Dvsa\Olcs\Api\Service\Nysiis\NysiisSoapClient;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Soap\Client as SoapClient;

/**
 * Class NysiisSoapClientTest
 * @package Dvsa\OlcsTest\Api\Service\Data
 */
class NysiisSoapClientTest extends MockeryTestCase
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
        $soapClient->shouldReceive('GetNYSIISSearchKeys')
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

        $sut = new NysiisSoapClient($soapClient);

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

        $soapClient->shouldReceive('GetNYSIISSearchKeys')
            ->once()
            ->with($inputData)
            ->andThrowExceptions([new \SoapFault('SOAP-ERROR', 'Soap error message')]);

        $sut = new NysiisSoapClient($soapClient);
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
