<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class NumberOfPermitsMoroccoQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NumberOfPermitsMoroccoQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NumberOfPermitsMoroccoQuestionHandler
    {
        return $this->__invoke($serviceLocator, NumberOfPermitsMoroccoQuestionHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return NumberOfPermitsMoroccoQuestionHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NumberOfPermitsMoroccoQuestionHandler
    {
        return new NumberOfPermitsMoroccoQuestionHandler(
            $container->get('PermitsBilateralCommonNoOfPermitsConditionalUpdater')
        );
    }
}
