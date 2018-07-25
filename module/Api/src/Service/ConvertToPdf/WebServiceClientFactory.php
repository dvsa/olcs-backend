<?php

namespace Dvsa\Olcs\Api\Service\ConvertToPdf;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as HttpClient;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;

/**
 * Class WebServiceClientFactory
 */
class WebServiceClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return WebServiceClient
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['convert_to_pdf']['uri'])) {
            throw new \RuntimeException('Missing print service config[convert_to_pdf][uri]');
        }

        $options = isset($config['convert_to_pdf']['options']) ? $config['convert_to_pdf']['options'] : [];
        $httpClient = new HttpClient($config['convert_to_pdf']['uri'], $options);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);
        $wrapper->setShouldLogData(false);

        return new WebServiceClient($httpClient);
    }
}
