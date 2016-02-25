<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Zend\Http\Client as RestClient;
use Zend\Http\Request;

/**
 * Class InrClient
 * @package Dvsa\Olcs\Api\Service\Nr
 */
class InrClient implements InrClientInterface
{
    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @param RestClient $restClient
     */
    public function __construct($restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * Makes a request to INR with penalty information
     * @param String $xml
     * @return String $result
     */
    public function makeRequest($xml)
    {
        $this->restClient->getRequest()->setMethod(Request::METHOD_POST);
        $this->restClient->getRequest()->setContent($xml);
        $response = $this->restClient->send();

        return $response->getStatusCode();
    }
}
