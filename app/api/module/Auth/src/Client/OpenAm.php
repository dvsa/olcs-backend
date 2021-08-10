<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Header\ContentType;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Dvsa\Olcs\Auth\Service\OpenAm\Callback\NameCallback;
use Dvsa\Olcs\Auth\Service\OpenAm\Callback\PasswordCallback;
use Dvsa\Olcs\Auth\Service\OpenAm\Callback\Request as AuthRequest;

/**
 * @todo this has been copied and adapted from the old olcs-auth package for backward compatibility with OpenAm
 * This code can be removed once migration to Cognito is complete
 */
class OpenAm
{
    const AUTHENTICATE_URI = '/json/authenticate';
    const MSG_SESSION_START_FAIL = 'Unable to begin an authentication session';
    const MSG_JSON_ENCODE_FAIL = 'POST data could not be json encoded: %s';
    const MSG_JSON_DECODE_FAIL = 'Unable to JSON decode response body: %s';

    /**
     * @var UriBuilder
     */
    private $uriBuilder;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(UriBuilder $uriBuilder, HttpClient $httpClient)
    {
        $this->uriBuilder = $uriBuilder;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    public function authenticate(string $username, string $password): array
    {
        $data = $this->beginAuthenticationSession();
        $request = $this->buildRequest($data['authId'], $username, $password);

        return $this->makeRequest(self::AUTHENTICATE_URI, $request->toArray());
    }

    /**
     * @param string       $uri
     * @param array        $data
     * @param Headers|null $headers
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    public function makeRequest(string $uri, array $data = [], Headers $headers = null): array
    {
        $response = $this->post($uri, $data, $headers);
        return $this->decodeResponse($response);
    }

    /**
     * @param string $token
     *
     * @return array
     * @throws InvalidTokenException when the token provided is invalid and cannot be decoded.
     */
    private function decodeToken(string $token): array
    {
        $decoded = json_decode($token, true);

        if ($decoded === null) {
            throw new InvalidTokenException(
                sprintf(self::MSG_JSON_DECODE_FAIL, json_last_error_msg())
            );
        }

        return $decoded;
    }

    /**
     * Decode response content
     *
     * @param Response $response Response
     *
     * @return array
     * @throws InvalidTokenException
     */
    private function decodeResponse(Response $response): array
    {
        $content = $response->getContent();
        $decoded = $this->decodeToken($content);
        $decoded['status'] = $response->getStatusCode();
        $decoded['provider'] = __CLASS__;

        return $decoded;
    }

    /**
     * Send a POST
     *
     * @param string       $uri     URI
     * @param array        $data    Data
     * @param Headers|null $headers Headers
     *
     * @return Response
     */
    private function post(string $uri, array $data = [], Headers $headers = null)
    {
        $this->httpClient->reset();
        $this->httpClient->setMethod(Request::METHOD_POST);
        $this->httpClient->setUri($this->uriBuilder->build($uri));

        if ($headers === null) {
            $headers = new Headers();
        }

        $headers->addHeader(new ContentType('application/json'));

        $this->httpClient->setHeaders($headers);

        if (!empty($data)) {
            $jsonData = json_encode($data);

            if ($jsonData === false) {
                throw new ClientException(
                    sprintf(self::MSG_JSON_ENCODE_FAIL, json_last_error_msg())
                );
            }

            $this->httpClient->setRawBody($jsonData);
        }

        return $this->httpClient->send();
    }

    /**
     * Build the request object
     *
     * @param string $authId   Auth id
     * @param string $username Username
     * @param string $password Password
     *
     * @return AuthRequest
     */
    private function buildRequest(string $authId, string $username, string $password): AuthRequest
    {
        $request = new AuthRequest($authId, AuthRequest::STAGE_AUTHENTICATE);
        $request->addCallback(new NameCallback('User Name:', 'IDToken1', $username));
        $request->addCallback(new PasswordCallback('Password:', 'IDToken2', $password));

        return $request;
    }

    /**
     * Begin an authentication session in OpenAM
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    private function beginAuthenticationSession(): array
    {
        $response = $this->post(self::AUTHENTICATE_URI);

        if ($response->isOk()) {
            return $this->decodeResponse($response);
        }

        throw new ClientException(self::MSG_SESSION_START_FAIL);
    }
}
