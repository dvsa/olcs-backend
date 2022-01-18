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
use Olcs\Logging\Log\Logger;

/**
 * @todo this has been copied and adapted from the old olcs-auth package for backward compatibility with OpenAm
 * This code can be removed once migration to Cognito is complete
 */
class OpenAm
{
    const AUTHENTICATE_URI = '/json/authenticate';
    const CHANGE_PW_URI = 'json/users/%s?_action=changePassword';
    const FORGOT_PW_URI = 'json/users/?_action=forgotPassword';
    const RESET_PW_URI = 'json/users?_action=forgotPasswordReset';
    const RESET_PW_CONFIRM_URI = 'json/users?_action=confirm';
    const MSG_SESSION_START_FAIL = 'Unable to begin an authentication session';
    const MSG_JSON_ENCODE_FAIL = 'POST data could not be json encoded: %s';
    const MSG_JSON_DECODE_FAIL = 'Unable to JSON decode response body: %s';
    const OPEN_AM_EXCEPTION = 'OpenAm returned exception';

    private UriBuilder $uriBuilder;
    private HttpClient $httpClient;
    private string $cookieName;

    public function __construct(UriBuilder $uriBuilder, HttpClient $httpClient, string $cookieName)
    {
        $this->uriBuilder = $uriBuilder;
        $this->httpClient = $httpClient;
        $this->cookieName = $cookieName;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    public function authenticate(string $username, string $password, string $realm): array
    {
        $this->uriBuilder->setRealm($realm);

        $data = $this->beginAuthenticationSession();
        $request = $this->buildRequest($data['authId'], $username, $password);

        return $this->makeRequest(self::AUTHENTICATE_URI, $request->toArray());
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $newPassword
     * @param string $realm
     * @param string $token
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    public function changePassword(
        string $username,
        string $password,
        string $newPassword,
        string $realm,
        string $token
    ): array {
        $this->uriBuilder->setRealm($realm);

        $headers = new Headers();
        $headers->addHeaderLine($this->cookieName, $token);

        $uri = sprintf(self::CHANGE_PW_URI, $username);

        $data = [
            'currentpassword' => $password,
            'userpassword' => $newPassword,
        ];

        return $this->makeRequest($uri, $data, $headers);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $newPassword
     * @param string $realm
     *
     * @return array
     * @throws ClientException
     */
    public function forgotPassword(string $username, string $subject, string $message, string $realm): array
    {
        $this->uriBuilder->setRealm($realm);

        $data = [
            'username' => $username,
            'subject' => $subject,
            'message' => $message,
        ];

        return $this->makeRequest(self::FORGOT_PW_URI, $data);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $confirmationId
     * @param string $realm
     * @param string $token
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    public function resetPassword(
        string $username,
        string $password,
        string $confirmationId,
        string $token,
        string $realm
    ): array {
        $this->uriBuilder->setRealm($realm);

        $data = [
            'username' => $username,
            'userpassword' => $password,
            'tokenId' => $token,
            'confirmationId' => $confirmationId
        ];

        return $this->makeRequest(self::RESET_PW_URI, $data);
    }

    /**
     * @param string $username
     * @param string $confirmationId
     * @param string $token
     * @param string $realm
     *
     * @return array
     * @throws ClientException
     * @throws InvalidTokenException
     */
    public function confirmPasswordResetValid(
        string $username,
        string $confirmationId,
        string $token,
        string $realm
    ): array {
        $this->uriBuilder->setRealm($realm);

        $data = [
            'username' => $username,
            'tokenId' => $token,
            'confirmationId' => $confirmationId
        ];

        return $this->makeRequest(self::RESET_PW_CONFIRM_URI, $data);
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

        Logger::err(static::OPEN_AM_EXCEPTION, [$response->getBody()]);
        throw new ClientException(self::MSG_SESSION_START_FAIL);
    }
}
