<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class IntJourneysGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IntJourneysGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IntJourneysGenerator
    {
        return $this->__invoke($serviceLocator, IntJourneysGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IntJourneysGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IntJourneysGenerator
    {
        return new IntJourneysGenerator(
            $container->get('QaEcmtIntJourneysElementFactory'),
            $container->get('QaRadioElementGenerator')
        );
    }
}
