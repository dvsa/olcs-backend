<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Laminas\Filter\FilterPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Validator\Xsd;
use Laminas\Http\Client as RestClient;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;

/**
 * Class TransExchangeClientFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr
 */
class TransExchangeClientFactory implements FactoryInterface
{
    const PUBLISH_XSD = 'http://naptan.dft.gov.uk/transxchange/publisher/schema/3.1.2/TransXChangePublisherService.xsd';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['ebsr']['transexchange_publisher'])) {
            throw new \RuntimeException('Missing transexchange_publisher config');
        }

        $config = $config['ebsr']['transexchange_publisher'];

        $httpClient = new RestClient($config['uri'], $config['options']);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);

        /**
         * @var FilterPluginManager $filterManager
         * @var MapXmlFile $xmlFilter
         * @var ParseXmlString $xmlParser
         * @var Xsd $xsdValidator
         */
        $filterManager = $serviceLocator->get('FilterManager');

        $xmlParser = $filterManager->get(ParseXmlString::class);

        $xmlFilter = $filterManager->get(MapXmlFile::class);
        $xmlFilter->setMapping($serviceLocator->get('TransExchangePublisherXmlMapping'));

        $xsdValidator = $serviceLocator->get('ValidatorManager')->get(Xsd::class);
        $xsdValidator->setXsd(self::PUBLISH_XSD);

        return new TransExchangeClient($httpClient, $xmlFilter, $xmlParser, $xsdValidator);
    }
}
