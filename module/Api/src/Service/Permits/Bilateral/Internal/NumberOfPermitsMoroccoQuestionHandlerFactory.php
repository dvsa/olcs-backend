<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NumberOfPermitsMoroccoQuestionHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NumberOfPermitsMoroccoQuestionHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NumberOfPermitsMoroccoQuestionHandler(
            $serviceLocator->get('PermitsBilateralCommonNoOfPermitsConditionalUpdater')
        );
    }
}
