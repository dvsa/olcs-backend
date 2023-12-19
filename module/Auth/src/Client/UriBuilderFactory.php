<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UriBuilderFactory implements FactoryInterface
{
    public const MSG_MISSING_INTERNAL_URL = 'openam/urls/internal is required but missing from config';
    public const MSG_MISSING_SELFSERVE_URL = 'openam/urls/selfserve is required but missing from config';

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return UriBuilder
     * @throws ClientException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UriBuilder
    {
        $config = $container->get('Config');

        if (empty($config['auth']['adapters']['openam']['urls']['internal'] ?? null)) {
            throw new ClientException(self::MSG_MISSING_INTERNAL_URL);
        }

        if (empty($config['auth']['adapters']['openam']['urls']['selfserve'] ?? null)) {
            throw new ClientException(self::MSG_MISSING_SELFSERVE_URL);
        }

        $internalUrl = $config['auth']['adapters']['openam']['urls']['internal'];
        $selfserveUrl = $config['auth']['adapters']['openam']['urls']['selfserve'];

        return new UriBuilder($internalUrl, $selfserveUrl);
    }
}
