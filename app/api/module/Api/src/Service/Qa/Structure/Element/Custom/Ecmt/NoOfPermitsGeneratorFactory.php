<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NoOfPermitsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsGenerator
    {
        return $this->__invoke($serviceLocator, NoOfPermitsGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NoOfPermitsGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsGenerator
    {
        return new NoOfPermitsGenerator(
            $container->get('RepositoryServiceManager')->get('FeeType'),
            $container->get('QaEcmtNoOfPermitsElementFactory'),
            $container->get('QaEcmtEmissionsCategoryConditionalAdder'),
            $container->get('PermitsAvailabilityStockAvailabilityCounter'),
            $container->get('PermitsAvailabilityStockLicenceMaxPermittedCounter')
        );
    }
}
