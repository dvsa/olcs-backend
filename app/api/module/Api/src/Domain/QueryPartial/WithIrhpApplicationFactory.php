<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class WithIrhpApplicationFactory implements FactoryInterface
{
    /**
     * Create service can be removed following Laminas v3 upgrade
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return WithIrhpApplication
     */
    public function createService(ServiceLocatorInterface $serviceLocator): WithIrhpApplication
    {
        return $this($serviceLocator, WithIrhpApplication::class);
    }
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return WithIrhpApplication
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WithIrhpApplication
    {
        return new WithIrhpApplication(
            $container->get('with')
        );
    }

}
