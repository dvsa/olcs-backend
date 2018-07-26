<?php

namespace Dvsa\Olcs\Api\Service\Nysiis;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as RestClient;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;

/**
 * Class NysiisRestClientFactory
 * @package Dvsa\Olcs\Api\Service\Nysiis
 */
class NysiisRestClientFactory implements FactoryInterface
{
    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return NysiisRestClient
     * @throws \RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        if (!isset($config['nysiis']['rest']['uri'])) {
            throw new \RuntimeException('Missing nysiis rest client uri');
        }

        if (!isset($config['nysiis']['rest']['options'])) {
            throw new \RuntimeException('Missing nysiis rest client options');
        }

        $httpClient = new RestClient($config['nysiis']['rest']['uri'], $config['nysiis']['rest']['options']);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);

        return new NysiisRestClient($httpClient);
    }
}
