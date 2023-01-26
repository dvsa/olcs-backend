<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PermitUsageQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitUsageQuestionHandler
    {
        return $this->__invoke($serviceLocator, PermitUsageQuestionHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PermitUsageQuestionHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageQuestionHandler
    {
        return new PermitUsageQuestionHandler(
            $container->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $container->get('PermitsBilateralCommonPermitUsageUpdater')
        );
    }
}
