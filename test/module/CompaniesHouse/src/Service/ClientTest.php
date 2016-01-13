<?php

namespace Dvsa\OlcsTest\CompaniesHouse\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\CompaniesHouse\Service\Client;

/**
 * ClientTest
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ClientTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Client();
    }

    public function testGetCompanyProfile()
    {
        $companyNumber = '03127414';

        $this->sut->setBaseUri('BASE_URI');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);

        $this->sut->setHttpClient($mockHttpClient);

        $mockHttpClient->shouldReceive('getRequest')->with()->once()->andReturn($mockRequest);
        $mockRequest->shouldReceive('setUri')->with('BASE_URI/company/03127414')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setMethod')->with('GET')->once()->andReturnSelf();

        $response = new \Zend\Http\Response();
        $response->setStatusCode(200);
        $response->setContent('{"foo":"bar"}');

        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($response);

        $this->assertEquals(['foo' => 'bar'], $this->sut->getCompanyProfile($companyNumber, false));
    }

    public function testGetCompanyProfileWithOfficers()
    {
        $companyNumber = '03127414';

        $this->sut->setBaseUri('BASE_URI');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);

        $this->sut->setHttpClient($mockHttpClient);

        $mockHttpClient->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockRequest->shouldReceive('setMethod')->with('GET')->andReturnSelf();
        $mockRequest->shouldReceive('setUri')->with('BASE_URI/company/03127414')->once()->andReturnSelf();
        $mockRequest->shouldReceive('setUri')->with('BASE_URI/company/03127414/officers')->once()->andReturnSelf();

        $companyResponse = new \Zend\Http\Response();
        $companyResponse->setStatusCode(200);
        $companyResponse->setContent('{"foo":"bar"}');

        $officersResponse = new \Zend\Http\Response();
        $officersResponse->setStatusCode(200);
        $officersResponse->setContent('{"items": {"0":{"name":"Bob"}, "1":{"name":"Dave"}}}');

        $mockHttpClient
            ->shouldReceive('send')
            ->andReturn($companyResponse, $officersResponse);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'officer_summary' => [
                    'officers' => [
                        ['name' => 'Bob'],
                        ['name' => 'Dave'],
                    ]
                ]
            ],
            $this->sut->getCompanyProfile($companyNumber, true)
        );
    }

    public function testGetCompanyProfileErrorResponse()
    {
        $companyNumber = '03127414';

        $this->sut->setBaseUri('BASE_URI');

        $mockRequest = m::mock(\Zend\Http\Request::class);
        $mockHttpClient = m::mock(\Zend\Http\Client::class);

        $this->sut->setHttpClient($mockHttpClient);

        $mockHttpClient->shouldReceive('getRequest')->andReturn($mockRequest);
        $mockRequest->shouldReceive('setMethod')->with('GET')->andReturnSelf();
        $mockRequest->shouldReceive('setUri')->with('BASE_URI/company/03127414')->once()->andReturnSelf();

        $companyResponse = new \Zend\Http\Response();
        $companyResponse->setStatusCode(404);
        $companyResponse->setContent('{"error":"not found"}');

        $mockHttpClient
            ->shouldReceive('send')
            ->andReturn($companyResponse);

        $this->setExpectedException(\Dvsa\Olcs\CompaniesHouse\Service\Exception::class);

        $this->sut->getCompanyProfile($companyNumber);
    }
}
