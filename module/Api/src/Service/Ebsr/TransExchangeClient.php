<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\Http\Client as RestClient;

/**
 * Class TransExchangeClient
 * @package Olcs\Ebsr\Service
 */
class TransExchangeClient implements TransExchangeClientInterface
{
    const REQUEST_MAP_TEMPLATE = 'RequestMap';
    const GENERATE_DOCS_TEMPLATE = 'Standard';

    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * @var MapXmlFile
     */
    private $xmlFilter;

    /**
     * @param $restClient
     * @param $xmlFilter
     */
    public function __construct($restClient, $xmlFilter)
    {
        $this->restClient = $restClient;
        $this->xmlFilter = $xmlFilter;
    }

    /**
     * @param string $content
     * @return array
     */
    public function makeRequest($content)
    {
        $this->restClient->getRequest()->setContent($content);
        $response = $this->restClient->send();
        $body = $response->getContent();

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $result = $this->xmlFilter->filter($dom);

        return $result;
    }
}
