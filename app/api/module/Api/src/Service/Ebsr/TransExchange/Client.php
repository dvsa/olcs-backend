<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\TransExchange;

use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\Http\Client as RestClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Client
 * @package Olcs\Ebsr\Service
 */
class Client implements FactoryInterface
{
    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @var MapXmlFile
     */
    protected $xmlFilter;

    /**
     * @return RestClient
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * @param RestClient $restClient
     */
    public function setRestClient($restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * @return mixed
     */
    public function getXmlFilter()
    {
        return $this->xmlFilter;
    }

    /**
     * @param mixed $xmlFilter
     */
    public function setXmlFilter($xmlFilter)
    {
        $this->xmlFilter = $xmlFilter;
    }

    /**
     * @param Template $template
     */
    public function makeRequest(Template $template)
    {
        $this->restClient->getRequest()->setContent($template->render());
        $response = $this->restClient->send();
        $body = $response->getContent();

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $result = $this->getXmlFilter()->filter($dom);

        return $result;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['ebsr'];

        if (!isset($config['transexchange_publisher'])) {
            throw new \RuntimeException('Missing transexchange_publisher config');
        }

        $config = $config['transexchange_publisher'];

        $httpClient = new RestClient($config['uri'], $config['options']);

        $this->setRestClient($httpClient);

        /**
         * @var MapXmlFile $filter
         */
        $filter = $serviceLocator->get('FilterManager')->get('MapXmlFile');
        $filter->setMapping($serviceLocator->get('TransExchangePublisherXml'));

        $this->setXmlFilter($filter);

        return $this;
    }
}
