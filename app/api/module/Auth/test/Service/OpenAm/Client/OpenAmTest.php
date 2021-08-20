<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service\OpenAm\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Dvsa\Olcs\Auth\Client\UriBuilder;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Headers as HttpHeaders;
use Laminas\Http\Request as HttpRequest;
use Laminas\Http\Response as HttpResponse;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OpenAmTest extends MockeryTestCase
{
    public function testAuthenticate(): void
    {
        $identity = 'identity';
        $password = 'password';
        $realm = 'realm';
        $uri = 'http://hostname:123';
        $builtUri = 'http://hostname:123/foo/bar';
        $authId = '12345';

        $uriBuilder = m::mock(UriBuilder::class);
        $uriBuilder->expects('build')->twice()->with(OpenAmClient::AUTHENTICATE_URI)->andReturn($builtUri);
        $uriBuilder->expects('setRealm')->once()->with($realm);

        $authSessionStartContent = json_encode([
            'authId' => $authId
        ]);

        $httpResponse = m::mock(HttpResponse::class);
        $httpResponse->expects('isOk')->andReturnTrue();
        $httpResponse->expects('getContent')->twice()->withNoArgs()->andReturn($authSessionStartContent);
        $httpResponse->expects('getStatusCode')->twice()->andReturn(200);

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->twice()->withNoArgs();
        $httpClient->expects('setMethod')->twice()->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->twice()->with($builtUri);
        $httpClient->expects('setHeaders')->twice()->with(m::type(HttpHeaders::class));
        $httpClient->expects('setRawBody');
        $httpClient->expects('send')->twice()->andReturn($httpResponse);

        $sut = new OpenAmClient($uriBuilder, $httpClient);
        $sut->authenticate($identity, $password, $realm);
    }

    public function testAuthenticateFailedToBeginSession(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(OpenAmClient::MSG_SESSION_START_FAIL);

        $identity = 'identity';
        $password = 'password';
        $realm = 'realm';
        $builtUri = 'http://hostname:123/foo/bar';

        $uriBuilder = m::mock(UriBuilder::class);
        $uriBuilder->expects('build')->with(OpenAmClient::AUTHENTICATE_URI)->andReturn($builtUri);
        $uriBuilder->expects('setRealm')->once()->with($realm);

        $httpResponse = m::mock(HttpResponse::class);
        $httpResponse->expects('isOk')->andReturnFalse();

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->withNoArgs();
        $httpClient->expects('setMethod')->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->with($builtUri);
        $httpClient->expects('setHeaders')->with(m::type(HttpHeaders::class));
        $httpClient->expects('send')->withNoArgs()->andReturn($httpResponse);

        $sut = new OpenAmClient($uriBuilder, $httpClient);
        $sut->authenticate($identity, $password, $realm);
    }

    public function testFailedToEncodeRequest(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            sprintf(OpenAmClient::MSG_JSON_ENCODE_FAIL, 'Recursion detected')
        );

        $brokenJson = [&$brokenJson];

        $builtUri = 'http://hostname:123/foo/bar';

        $uriBuilder = m::mock(UriBuilder::class);
        $uriBuilder->expects('build')->with(OpenAmClient::AUTHENTICATE_URI)->andReturn($builtUri);

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->withNoArgs();
        $httpClient->expects('setMethod')->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->with($builtUri);
        $httpClient->expects('setHeaders')->with(m::type(HttpHeaders::class));

        $sut = new OpenAmClient($uriBuilder, $httpClient);
        $sut->makeRequest(OpenAmClient::AUTHENTICATE_URI, $brokenJson);
    }

    public function testFailedToDecodeResponse(): void
    {
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage(
            sprintf(OpenAmClient::MSG_JSON_DECODE_FAIL, 'Syntax error')
        );

        $data = ['data' => 'data'];
        $encodedData = json_encode($data);
        $brokenJson = '{';

        $builtUri = 'http://hostname:123/foo/bar';

        $httpResponse = m::mock(HttpResponse::class);
        $httpResponse->expects('getContent')->andReturn($brokenJson);

        $uriBuilder = m::mock(UriBuilder::class);
        $uriBuilder->expects('build')->with(OpenAmClient::AUTHENTICATE_URI)->andReturn($builtUri);

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->withNoArgs();
        $httpClient->expects('setMethod')->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->with($builtUri);
        $httpClient->expects('setHeaders')->with(m::type(HttpHeaders::class));
        $httpClient->expects('setRawBody')->with($encodedData);
        $httpClient->expects('send')->withNoArgs()->andREturn($httpResponse);

        $sut = new OpenAmClient($uriBuilder, $httpClient);
        $sut->makeRequest(OpenAmClient::AUTHENTICATE_URI, $data);
    }
}
