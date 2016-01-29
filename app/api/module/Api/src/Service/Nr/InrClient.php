<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Zend\Http\Client as RestClient;

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
     * @todo We don't have anywhere to post our request, so returns 202 for now. Uncomment code when the time comes
     * @param String $xml
     * @return String $result
     */
    public function makeRequest($xml)
    {
        //$this->restClient->getRequest()->setContent($xml);
        //$response = $this->restClient->send();

        //return $response->getStatusCode();
        return 202;
    }
}
