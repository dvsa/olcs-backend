<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Uri\Http as Uri;

/**
 * Class Client
 * @package Dvsa\Olcs\Api\Service\OpenAm
 */
class Client implements ClientInterface
{
    const REALM_INTERNAL = 'internal';
    const REALM_SELFSERVE = 'selfserve';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Request
     */
    private $templateRequest;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient, Request $templateRequest)
    {
        $this->httpClient = $httpClient;
        $this->templateRequest = $templateRequest;
    }

    /**
     * Registers a user
     *
     * @param string $username
     * @param string $pid
     * @param string $emailAddress
     * @param string $surname
     * @param string $commonName
     * @param string $realm
     * @param string $password
     *
     * @return void
     * @throws FailedRequestException
     */
    public function registerUser($username, $pid, $emailAddress, $surname, $commonName, $realm, $password)
    {
        $payload = [
            '_id' => $pid,
            'pid' => $pid,
            'userName' => $username,
            'emailAddress' => $emailAddress,
            'surName' => $surname,
            'commonName' => $commonName,
            'realm' => $realm,
            'password' => $password
        ];

        $request = $this->createRequest('/users?_action=create', Request::METHOD_POST);
        $request->setContent(json_encode($payload));

        $response = $this->httpClient->send($request);

        if (!$response->isSuccess()) {
            throw new FailedRequestException($response);
        }
    }

    /**
     * Updates a user
     *
     * @param string $username
     * @param array $updates
     *
     * @return void
     * @throws FailedRequestException
     */
    public function updateUser($username, $updates)
    {
        $request = $this->createRequest('/users/' . $username, Request::METHOD_PATCH);
        $request->setContent(json_encode($updates));

        $response = $this->httpClient->send($request);

        if (!$response->isSuccess()) {
            throw new FailedRequestException($response);
        }
    }

    /**
     * Creates a request
     *
     * @param string $path
     * @param string $method
     *
     * @return Request
     */
    private function createRequest($path, $method)
    {
        $request = clone $this->templateRequest;
        $request->setMethod($method);
        $request->setUri(Uri::merge($request->getUriString(), $path));

        return $request;
    }
}
