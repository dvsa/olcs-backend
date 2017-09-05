<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Zend\Http\Client as RestClient;
use Zend\Http\Request;
use Olcs\Logging\Log\Logger;

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
     * Contructor, expects zend rest client
     *
     * @param RestClient $restClient zend rest client
     *
     * @return void
     */
    public function __construct($restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * Makes a request to INR with penalty information
     *
     * @param String $xml the xml string being sent
     *
     * @return String
     */
    public function makeRequest($xml)
    {
        $this->restClient->setEncType('text/xml');
        $this->restClient->getRequest()->setMethod(Request::METHOD_POST);
        $this->restClient->getRequest()->setContent($xml);

        Logger::info('INR request', ['data' => $this->restClient->getRequest()->toString()]);

        $response = $this->restClient->send();

        Logger::info('INR response', ['data' => $response->toString()]);

        return $response->getStatusCode();
    }

    /**
     * Get the rest client
     *
     * @return RestClient
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * close connection to INR
     *
     * @return void
     */
    public function close()
    {
        $this->restClient->getAdapter()->close();
    }
}
