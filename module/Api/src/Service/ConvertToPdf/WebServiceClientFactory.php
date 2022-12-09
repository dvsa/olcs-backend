<?php

namespace Dvsa\Olcs\Api\Service\ConvertToPdf;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Client as HttpClient;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator): WebServiceClient
    {
        return $this->__invoke($serviceLocator, WebServiceClient::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return WebServiceClient
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WebServiceClient
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
