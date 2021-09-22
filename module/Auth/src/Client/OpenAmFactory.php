<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Interop\Container\ContainerInterface;
use Laminas\Http\Client as HttpClient;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class OpenAmFactory implements FactoryInterface
{
    const MSG_MISSING_OPTIONS = 'OpenAm client options missing from config';
    const MSG_MISSING_COOKIE_NAME = 'OpenAm cookie name missing from config';

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return OpenAmClient
     * @throws ClientException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): OpenAmClient
    {
        $config = $container->get('Config');

        if (!isset($config['auth']['adapters']['openam']['client']['options'])) {
            throw new ClientException(self::MSG_MISSING_OPTIONS);
        }

        if (!isset($config['auth']['adapters']['openam']['cookie']['name'])) {
            throw new ClientException(self::MSG_MISSING_COOKIE_NAME);
        }

        return new OpenAmClient(
            $container->get(UriBuilder::class),
            new HttpClient(null, $config['auth']['adapters']['openam']['client']['options']),
            $config['auth']['adapters']['openam']['cookie']['name']
        );
    }

    /**
     * @deprecated can be removed following Laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OpenAmClient
     * @throws ClientException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): OpenAmClient
    {
        return $this($serviceLocator, OpenAmClient::class);
    }
}
