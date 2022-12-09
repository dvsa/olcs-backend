<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ExistingIrhpPermitApplicationHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ExistingIrhpPermitApplicationHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ExistingIrhpPermitApplicationHandler
    {
        return $this->__invoke($serviceLocator, ExistingIrhpPermitApplicationHandler::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ExistingIrhpPermitApplicationHandler
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ExistingIrhpPermitApplicationHandler
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new ExistingIrhpPermitApplicationHandler(
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('IrhpPermitStock'),
            $container->get('QaApplicationAnswersClearer'),
            $container->get('PermitsBilateralInternalQuestionHandlerDelegator')
        );
    }
}
