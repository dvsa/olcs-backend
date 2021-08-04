<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class UriBuilderFactory implements FactoryInterface
{
    const MSG_MISSING_URL = 'openam/url is required but missing from config';

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return UriBuilder
     * @throws ClientException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UriBuilder
    {
        $config = $container->get('Config');

        if (empty($config['auth']['adapters']['openam']['url'])) {
            throw new ClientException(self::MSG_MISSING_URL);
        }

        $baseUrl = $config['auth']['adapters']['openam']['url'];
        $realm = null;

        if (isset($config['auth']['adapters']['openam']['realm'])) {
            $realm = $config['auth']['adapters']['openam']['realm'];
        }

        return new UriBuilder($baseUrl, $realm);
    }

    /**
     * @deprecated can be removed following Laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UriBuilder
     * @throws ClientException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): UriBuilder
    {
        return $this($serviceLocator, UriBuilder::class);
    }
}
