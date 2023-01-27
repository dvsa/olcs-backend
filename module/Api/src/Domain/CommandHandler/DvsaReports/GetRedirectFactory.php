<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DvsaReports;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Client;

class GetRedirectFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : GetRedirect
    {
        return (new GetRedirect(new Client()))->createService($container);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return GetRedirect
     * @deprecated Use __invoke
     */
    public function createService(ServiceLocatorInterface $serviceLocator) : GetRedirect
    {
        return $this->__invoke($serviceLocator, null);
    }
}
