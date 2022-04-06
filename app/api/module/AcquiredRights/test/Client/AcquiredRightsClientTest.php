<?php

namespace Dvsa\Olcs\AcquiredRights\Client;

use Dvsa\Olcs\AcquiredRights\Exception\MapperParseException;
use Dvsa\Olcs\AcquiredRights\Exception\ReferenceNotFoundException;
use Dvsa\Olcs\AcquiredRights\Exception\ServiceException;
use Dvsa\Olcs\AcquiredRights\Model\ApplicationReference;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Olcs\TestHelpers\MockeryTestCase;
use Mockery as m;

class AcquiredRightsClientTest extends MockeryTestCase
{
    protected $sut;
    protected $httpClient;

    public function setUp(): void
    {
        $this->httpClient = m::mock(Client::class);
        $this->sut = new AcquiredRightsClient($this->httpClient);
    }

    /**
     * @test
     */
    public function fetchByReference_ValidData_ReturnsApplicationReferenceModel()
    {
        $this->httpClient->shouldReceive('get')->andReturn($this->generateResponse(200, [
            'id' => '6fcf9551-ade4-4b48-b078-6db59559a182',
            'reference' => 'ABC1234',
            'status' => ApplicationReference::APPLICATION_STATUS_SUBMITTED,
            'submittedOn' => 'Mon, 13 Dec 2021 10:00:41 GMT',
            'dateOfBirth' => '2011-01-01T00:00:00.000Z',
        ]));

        $result = $this->sut->fetchByReference('ABC1234');

        $this->assertInstanceOf(ApplicationReference::class, $result);
    }

    /**
     * @test
     */
    public function fetchByReference_NotFound_ReturnsNotFoundException()
    {
        $this->expectException(ReferenceNotFoundException::class);

        $exception = ClientException::create(
            new Request('get', ''),
            new Response(404)
        );

        $this->httpClient->shouldReceive('get')->andThrow($exception);

        $this->sut->fetchByReference('ABC1234');
    }

    /**
     * @test
     */
    public function fetchByReference_ConnectException_ReturnsServiceException()
    {
        $this->expectException(ServiceException::class);

        $exception = ConnectException::create(
            new Request('get', '')
        );

        $this->httpClient->shouldReceive('get')->andThrow($exception);

        $this->sut->fetchByReference('ABC1234');
    }

    /**
     * @test
     */
    public function fetchByReference_RequestException_ReturnsServiceException()
    {
        $this->expectException(ServiceException::class);

        $exception = RequestException::create(
            new Request('get', '')
        );

        $this->httpClient->shouldReceive('get')->andThrow($exception);

        $this->sut->fetchByReference('ABC1234');
    }

    /**
     * @test
     */
    public function fetchByReference_InvalidJson_ReturnsServiceException()
    {
        $this->expectException(ServiceException::class);

        $exception = new \InvalidArgumentException();

        $this->httpClient->shouldReceive('get')->andThrow($exception);

        $this->sut->fetchByReference('ABC1234');
    }

    /**
     * @test
     */
    public function fetchByReference_MapperParseException_ReturnsMapperParseException()
    {
        $this->expectException(MapperParseException::class);

        $exception = new MapperParseException();

        $this->httpClient->shouldReceive('get')->andThrow($exception);

        $this->sut->fetchByReference('ABC1234');
    }

    protected function generateResponse(int $status, array $body): Response
    {
        return new Response($status, [], json_encode($body));
    }
}
