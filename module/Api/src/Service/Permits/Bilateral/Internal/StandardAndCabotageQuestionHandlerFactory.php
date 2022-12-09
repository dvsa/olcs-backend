<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StandardAndCabotageQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardAndCabotageQuestionHandler
    {
        return $this->__invoke($serviceLocator, StandardAndCabotageQuestionHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return StandardAndCabotageQuestionHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardAndCabotageQuestionHandler
    {
        return new StandardAndCabotageQuestionHandler(
            $container->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $container->get('PermitsBilateralInternalBilateralRequiredGenerator'),
            $container->get('PermitsBilateralCommonStandardAndCabotageUpdater')
        );
    }
}
