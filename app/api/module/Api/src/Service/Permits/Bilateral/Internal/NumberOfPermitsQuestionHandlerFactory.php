<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NumberOfPermitsQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NumberOfPermitsQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NumberOfPermitsQuestionHandler
    {
        return $this->__invoke($serviceLocator, NumberOfPermitsQuestionHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NumberOfPermitsQuestionHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NumberOfPermitsQuestionHandler
    {
        return new NumberOfPermitsQuestionHandler(
            $container->get('PermitsBilateralInternalPermitUsageSelectionGenerator'),
            $container->get('PermitsBilateralInternalBilateralRequiredGenerator'),
            $container->get('PermitsBilateralCommonNoOfPermitsConditionalUpdater')
        );
    }
}
