<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StandardAndCabotageAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardAndCabotageAnswerSaver
    {
        return $this->__invoke($serviceLocator, StandardAndCabotageAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StandardAndCabotageAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardAndCabotageAnswerSaver
    {
        return new StandardAndCabotageAnswerSaver(
            $container->get('QaNamedAnswerFetcher'),
            $container->get('PermitsBilateralCommonStandardAndCabotageUpdater')
        );
    }
}
