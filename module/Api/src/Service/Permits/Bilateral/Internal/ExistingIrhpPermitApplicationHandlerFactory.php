<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ExistingIrhpPermitApplicationHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ExistingIrhpPermitApplicationHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoServiceManager = $serviceLocator->get('RepositoryServiceManager');

        return new ExistingIrhpPermitApplicationHandler(
            $repoServiceManager->get('IrhpPermitApplication'),
            $repoServiceManager->get('IrhpPermitStock'),
            $serviceLocator->get('QaApplicationAnswersClearer'),
            $serviceLocator->get('PermitsBilateralInternalQuestionHandlerDelegator')
        );
    }
}
