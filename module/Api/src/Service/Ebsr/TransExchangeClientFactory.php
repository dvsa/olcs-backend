<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\AppRegistration\TransXChangeAppRegistrationService;
use Laminas\Filter\FilterPluginManager;
use Laminas\Http\Request;
use Laminas\Log\Processor\RequestId;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Validator\Xsd;
use Laminas\Http\Client as RestClient;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Psr\Container\ContainerInterface;

/**
 * Class TransExchangeClientFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr
 */
class TransExchangeClientFactory implements FactoryInterface
{
    public const PUBLISH_XSD = 'http://naptan.dft.gov.uk/transxchange/publisher/schema/3.1.2/TransXChangePublisherService.xsd';

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TransExchangeClient
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransExchangeClient
    {
        $config = $container->get('config');
        $transXChangeAppRegistrationService = $container->get(TransXChangeAppRegistrationService::class);

        $correlationId = (new RequestId())->process([])['extra']['requestId'];
        $token = $transXChangeAppRegistrationService->getToken();
        $headers = ['Authorization' => 'Bearer ' . $token];

        if (!isset($config['ebsr']['transexchange_publisher'])) {
            throw new \RuntimeException('Missing transexchange_publisher config');
        }
        $config = $config['ebsr']['transexchange_publisher'];

        $httpClient = new RestClient($config['new_uri'], $config['options']);
        $httpClient->setHeaders($headers);
        $httpClient->setMethod(Request::METHOD_POST);
        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);
        /**
         * @var FilterPluginManager $filterManager
         * @var MapXmlFile $xmlFilter
         * @var ParseXmlString $xmlParser
         * @var Xsd $xsdValidator
         */
        $filterManager = $container->get('FilterManager');
        $xmlParser = $filterManager->get(ParseXmlString::class);
        $xmlFilter = $filterManager->get(MapXmlFile::class);
        $xmlFilter->setMapping($container->get('TransExchangePublisherXmlMapping'));
        $xsdValidator = $container->get('ValidatorManager')->get(Xsd::class);
        $xsdValidator->setXsd(self::PUBLISH_XSD);
        return new TransExchangeClient($httpClient, $xmlFilter, $xmlParser, $xsdValidator, $correlationId);
    }
}
