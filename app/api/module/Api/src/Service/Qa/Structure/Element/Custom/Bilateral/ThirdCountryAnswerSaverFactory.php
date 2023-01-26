<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ThirdCountryAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ThirdCountryAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ThirdCountryAnswerSaver
    {
        return $this->__invoke($serviceLocator, ThirdCountryAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ThirdCountryAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ThirdCountryAnswerSaver
    {
        return new ThirdCountryAnswerSaver(
            $container->get('QaBilateralCountryDeletingAnswerSaver')
        );
    }
}
