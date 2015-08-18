<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Zend\Http\Client as HttpClient;
use Zend\Http\Header\Accept;
use Zend\Http\Request;

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
     * @var string
     */
    private $apiUsername;

    /**
     * @var string
     */
    private $apiPassword;

    /**
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient, $apiUsername, $apiPassword)
    {
        $this->httpClient = $httpClient;
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
    }

    /**
     * @param $username
     * @param $pid
     * @param $emailAddress
     * @param $surname
     * @param $commonName
     * @param $realm
     * @param $password
     * @throws FailedRequestException
     */
    public function registerUser($username, $pid, $emailAddress, $surname, $commonName, $realm, $password)
    {
        $payload = [
            '_id' => $username,
            'pid' => $pid,
            'emailAddress' => $emailAddress,
            'surName' => $surname,
            'commonName' => $commonName,
            'realm' => $realm,
            'password' => $password
        ];

        $request = $this->createRequest('/users?_action=create', Request::METHOD_POST);
        $request->setContent(json_encode($payload));

        $response = $this->httpClient->send();

        if (!$response->isSuccess()) {
            throw new FailedRequestException($response);
        }
    }

    /**
     * @param $path
     * @param $method
     * @return Request
     */
    private function createRequest($path, $method)
    {
        $request = $this->httpClient->getRequest();
        $request->setMethod($method);
        $request->getUri()->setPath($path);

        $headers = $request->getHeaders();
        $headers->addHeader(new Accept('application/json'));
        $headers->addHeaderLine('X-OpenIDM-Username', $this->apiUsername);
        $headers->addHeaderLine('X-OpenIDM-Password', $this->apiPassword);

        return $request;
    }
}