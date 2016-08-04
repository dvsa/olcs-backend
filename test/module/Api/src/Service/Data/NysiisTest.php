<?php

namespace Dvsa\OlcsTest\Api\Service\Data;

use Dvsa\Olcs\Api\Service\Data\Nysiis;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Soap\Client as SoapClient;
use Zend\Server\Client as ServerClient;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;

/**
 * Class IrfoPsvAuthType Test
 * @package CommonTest\Service
 */
class NysiisTest extends MockeryTestCase
{
    /**
     * testGetNysiisSearchKeys
     */
    public function testGetNysiisSearchKeys()
    {
        $source = [
            'nysiisForename' => 'fn',
            'nysiisFamilyname' => 'ln'
        ];

        $nysiisResult = new \StdClass();
        $nysiisResult->GetNYSIISSearchKeysResult = 'nysiis_result';

        $soapClient = m::mock(\SoapClient::class);
        $soapClient->shouldReceive('GetNYSIISSearchKeys')
            ->with(
                [
                    'firstName' => $source['nysiisForename'],
                    'familyName' => $source['nysiisFamilyname']
                ]
            )
            ->andReturn(
                $nysiisResult
            );

        $config = ['foo' => 'bar'];

        $sut = new Nysiis($soapClient, $config);

        $this->assertEquals('nysiis_result', $sut->getNysiisSearchKeys($source));
        $this->assertEquals($soapClient, $sut->getSoapClient());
        $this->assertEquals($config, $sut->getNysiisConfig());
    }

    /**
     * testGetNysiisSearchKeys Soap client failure
     */
    public function testGetNysiisSearchKeysSoapClientInvalid()
    {
        $soapClient = 'invalid';
        $config = ['foo' => 'bar'];

        $this->setExpectedException(NysiisException::class);

        $sut = new Nysiis($soapClient, $config);
    }

    /**
     * testGetNysiisSearchKeys returns bad result
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NysiisException
     */
    public function testGetNysiisSearchKeysReturnsBadResult()
    {
        $source = [
            'nysiisForename' => 'fn',
            'nysiisFamilyname' => 'ln'
        ];

        $nysiisResult = new \StdClass();
        $nysiisResult->SomeBadResult = 'nysiis_result';

        $soapClient = m::mock(\SoapClient::class);
        $soapClient->shouldReceive('GetNYSIISSearchKeys')
            ->with(
                [
                    'firstName' => $source['nysiisForename'],
                    'familyName' => $source['nysiisFamilyname']
                ]
            )
            ->andReturn(
                $nysiisResult
            );

        $config = ['foo' => 'bar'];

        $sut = new Nysiis($soapClient, $config);

        $this->assertEquals('nysiis_result', $sut->getNysiisSearchKeys($source));
        $this->assertEquals($soapClient, $sut->getSoapClient());
        $this->assertEquals($config, $sut->getNysiisConfig());
    }
}
