<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Adapter;

use Dvsa\Olcs\Auth\Adapter\OpenAm as OpenAmAdapter;
use Dvsa\Olcs\Auth\Client\OpenAm as OpenAmClient;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class OpenAmFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return OpenAmAdapter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): OpenAmAdapter
    {
        $client = $container->get(OpenAmClient::class);
        return new OpenAmAdapter($client);
    }

    /**
     * @deprecated Can be removed following Laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OpenAm
     */
    public function createService(ServiceLocatorInterface $serviceLocator): OpenAmAdapter
    {
        return $this($serviceLocator, OpenAmAdapter::class);
    }
}
