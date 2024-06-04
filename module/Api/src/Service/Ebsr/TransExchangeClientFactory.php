<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\AppRegistration\TransXChangeAppRegistrationService;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Laminas\Filter\FilterPluginManager;
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


    protected $toggleConfig = [
        'default' => FeatureToggle::BACKEND_TRANSXCHANGE
    ];

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
        $config = $container->get('Config');
        $transXChangeAppRegistrationService = $container->get(TransXChangeAppRegistrationService::class);
        /** @var RequestId $tequestId */
        $correlationId = (new RequestId())->process([])['extra']['requestId'];
        $token = $transXChangeAppRegistrationService->getToken();
        $headers = ['Authorization' => 'Bearer ' . $token];

        /** @var ToggleService $toggleService */
        $toggleService = $container->get(ToggleService::class);

        if (!isset($config['ebsr']['transexchange_publisher'])) {
            throw new \RuntimeException('Missing transexchange_publisher config');
        }
        $config = $config['ebsr']['transexchange_publisher'];
        if ( $toggleService->isEnabled(FeatureToggle::BACKEND_TRANSXCHANGE) ) {
            $config['uri'] = $config['new_uri'];
        }
        $httpClient = new RestClient($config['uri'], $config['options']);
        $httpClient->setHeaders($headers);
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
