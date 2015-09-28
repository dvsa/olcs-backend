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

    public function updateUser($username, $emailAddress = null, $commonName = null, $surName = null)
    {
        $payload = [];

        if ($emailAddress !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'emailAddress',
                'value' => $emailAddress
            ];
        }

        if ($commonName !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'commonName',
                'value' => $commonName
            ];
        }

        if ($surName !== null) {
            $payload[] = [
                'operation' => 'replace',
                'field' => 'surName',
                'value' => $surName
            ];
        }

        if (count($payload) === 0) {
            return;
        }

        $request = $this->createRequest('/users/' . $username, Request::METHOD_PATCH);
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
        $request = clone $this->templateRequest;
        $request->setMethod($method);
        $request->getUri()->setPath($path);

        return $request;
    }
}