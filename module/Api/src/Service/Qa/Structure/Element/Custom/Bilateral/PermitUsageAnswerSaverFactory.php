<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PermitUsageAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitUsageAnswerSaver
    {
        return $this->__invoke($serviceLocator, PermitUsageAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PermitUsageAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageAnswerSaver
    {
        return new PermitUsageAnswerSaver(
            $container->get('QaGenericAnswerFetcher'),
            $container->get('PermitsBilateralCommonPermitUsageUpdater')
        );
    }
}
