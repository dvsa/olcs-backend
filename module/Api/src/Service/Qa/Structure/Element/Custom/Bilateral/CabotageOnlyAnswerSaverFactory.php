<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CabotageOnlyAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageOnlyAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CabotageOnlyAnswerSaver
    {
        return $this->__invoke($serviceLocator, CabotageOnlyAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CabotageOnlyAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CabotageOnlyAnswerSaver
    {
        return new CabotageOnlyAnswerSaver(
            $container->get('QaBilateralCountryDeletingAnswerSaver')
        );
    }
}
