<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\Http\Client as RestClient;

class TransExchangeClientFactory implements FactoryInterface
{
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

        /**
         * @var MapXmlFile $filter
         */
        $filter = $serviceLocator->get('FilterManager')->get(MapXmlFile::class);
        $filter->setMapping($serviceLocator->get('TransExchangePublisherXmlMapping'));

        return new TransExchangeClient($httpClient, $filter);
    }
}