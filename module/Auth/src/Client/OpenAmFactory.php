<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Interop\Container\ContainerInterface;
use Laminas\Http\Client as HttpClient;
use Laminas\ServiceManager\Factory\FactoryInterface;

class OpenAmFactory implements FactoryInterface
{
    const MSG_MISSING_OPTIONS = 'OpenAm client options missing from config';
    const MSG_MISSING_COOKIE_NAME = 'OpenAm cookie name missing from config';

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OpenAm
     * @throws ClientException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
}
