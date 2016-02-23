<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;

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
        $inputName = 'processed_data';
        $service = new Input($inputName);
        $config = $serviceLocator->get('Config');

        $validatorChain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            $validatorChain->attach(
                $serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\BusRegNotFound'),
                true
            );
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
            $validatorChain->attach(
                $serviceLocator->get('ValidatorManager')->get('Rules\ProcessedData\VariationNumber')
            );
        }

        return $service;
    }
}
