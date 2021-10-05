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
        $httpResponse->expects('getStatusCode')->twice()->withNoArgs()->andReturn(200);

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->twice()->withNoArgs();
        $httpClient->expects('setMethod')->twice()->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->twice()->with($builtUri);
        $httpClient->expects('setHeaders')->twice()->with(m::type(HttpHeaders::class));
        $httpClient->expects('setRawBody');
        $httpClient->expects('send')->twice()->andReturn($httpResponse);

        $sut = new OpenAmClient($uriBuilder, $httpClient, 'cookie-name');
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
        $body = 'failed to begin session';

        $uriBuilder = m::mock(UriBuilder::class);
        $uriBuilder->expects('build')->with(OpenAmClient::AUTHENTICATE_URI)->andReturn($builtUri);
        $uriBuilder->expects('setRealm')->once()->with($realm);

        $httpResponse = m::mock(HttpResponse::class);
        $httpResponse->expects('isOk')->andReturnFalse();
        $httpResponse->expects('getBody')->andReturn($body);

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->withNoArgs();
        $httpClient->expects('setMethod')->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->with($builtUri);
        $httpClient->expects('setHeaders')->with(m::type(HttpHeaders::class));
        $httpClient->expects('send')->withNoArgs()->andReturn($httpResponse);

        $sut = new OpenAmClient($uriBuilder, $httpClient, 'cookie-name');
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

        $sut = new OpenAmClient($uriBuilder, $httpClient, 'cookie-name');
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
        $httpClient->expects('send')->withNoArgs()->andReturn($httpResponse);

        $sut = new OpenAmClient($uriBuilder, $httpClient, 'cookie-name');
        $sut->makeRequest(OpenAmClient::AUTHENTICATE_URI, $data);
    }

    public function testChangePassword()
    {
        $username = 'username';
        $oldPassword = 'old password';
        $newPassword = 'new password';
        $realm = 'realm';
        $token = 'token';
        $cookieName = 'cookie-name';

        $data = [
            'currentpassword' => $oldPassword,
            'userpassword' => $newPassword,
        ];

        $encodedData = json_encode($data);

        $builtUri = 'http://hostname:123/foo/bar';
        $uriBuilderUri = sprintf(OpenAmClient::CHANGE_PW_URI, $username);
        $uriBuilder = m::mock(UriBuilder::class);
        $uriBuilder->expects('setRealm')->with($realm);
        $uriBuilder->expects('build')->with($uriBuilderUri)->andReturn($builtUri);

        $responseContent = ['response' => 'response'];
        $responseCode = 200;

        $response = json_encode($responseContent);

        $httpResponse = m::mock(HttpResponse::class);
        $httpResponse->expects('getContent')->withNoArgs()->andReturn($response);
        $httpResponse->expects('getStatusCode')->withNoArgs()->andReturn($responseCode);

        $httpClient = m::mock(HttpClient::class);
        $httpClient->expects('reset')->withNoArgs();
        $httpClient->expects('setMethod')->with(HttpRequest::METHOD_POST);
        $httpClient->expects('setUri')->with($builtUri);
        $httpClient->expects('setHeaders')->with(m::type(HttpHeaders::class))->andReturnUsing(
            function (HttpHeaders $headers) use ($token, $cookieName) {
                $expectedHeaders = [
                    $cookieName => $token,
                    'Content-Type' => 'application/json',
                ];
                $this->assertEquals($expectedHeaders, $headers->toArray());

                return $headers;
            }
        );
        $httpClient->expects('setRawBody')->with($encodedData);
        $httpClient->expects('send')->withNoArgs()->andReturn($httpResponse);

        $clientResponse = [
            'response' => 'response',
            'status' => $responseCode,
            'provider' => OpenAmClient::class,
        ];

        $sut = new OpenAmClient($uriBuilder, $httpClient, $cookieName);
        $this->assertEquals(
            $clientResponse,
            $sut->changePassword($username, $oldPassword, $newPassword, $realm, $token)
        );
    }
}
