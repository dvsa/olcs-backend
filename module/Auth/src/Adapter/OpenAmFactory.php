<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Auth\Adapter\OpenAm as OpenAmAdapter;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class OpenAmFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OpenAm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): OpenAmAdapter
    {
        $client = $container->get(OpenAmClient::class);
        $identityProvider = $container->get(PidIdentityProvider::class);
        return new OpenAmAdapter($client, $identityProvider);
    }
}
