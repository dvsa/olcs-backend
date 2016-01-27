<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ProcessedDataInputFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr\InputFilter
 */
class ProcessedDataInputFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Input('processed_data');

        $validatorChain = $service->getValidatorChain();
        $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\BusRegNotFound'));
        $validatorChain->attach(
            $serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\NewAppAlreadyExists')
        );
        $validatorChain->attach(
            $serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\RegisteredBusRoute')
        );
        $validatorChain->attach(
            $serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\LocalAuthorityNotRequired')
        );
        $validatorChain->attach(
            $serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\LocalAuthorityMissing')
        );
        $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\VariationNumber'));

        return $service;
    }
}
