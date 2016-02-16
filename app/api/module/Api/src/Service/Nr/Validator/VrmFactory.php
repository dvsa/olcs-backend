<?php

namespace Dvsa\Olcs\Api\Service\Nr\Validator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Validators\Vrm as TransferVrmValidator;

/**
 * Class VrmFactory
 * @package Dvsa\Olcs\Api\Service\Nr\Validator
 */
class VrmFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Vrm();
        $service->setVrmValidator($serviceLocator->get(TransferVrmValidator::class));

        return $service;
    }
}
