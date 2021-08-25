<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class UriBuilderFactory implements FactoryInterface
{
    const MSG_MISSING_INTERNAL_URL = 'openam/urls/internal is required but missing from config';
    const MSG_MISSING_SELFSERVE_URL = 'openam/urls/selfserve is required but missing from config';

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

        if (empty($config['auth']['adapters']['openam']['urls']['internal']?? null)) {
            throw new ClientException(self::MSG_MISSING_INTERNAL_URL);
        }

        if (empty($config['auth']['adapters']['openam']['urls']['selfserve'] ?? null)) {
            throw new ClientException(self::MSG_MISSING_SELFSERVE_URL);
        }

        $internalUrl = $config['auth']['adapters']['openam']['urls']['internal'];
        $selfserveUrl = $config['auth']['adapters']['openam']['urls']['selfserve'];

        return new UriBuilder($internalUrl, $selfserveUrl);
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
