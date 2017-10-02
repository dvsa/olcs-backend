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
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return Input
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $inputName = 'processed_data';
        $service = new Input($inputName);
        $config = $serviceLocator->get('Config');

        $validatorChain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            /** @var ServiceLocatorInterface $validatorManager */
            $validatorManager = $serviceLocator->get('ValidatorManager');
            $validatorChain->attach($validatorManager->get('Rules\ProcessedData\BusRegNotFound'), true);
            $validatorChain->attach($validatorManager->get('Rules\ProcessedData\NewAppAlreadyExists'));
            $validatorChain->attach($validatorManager->get('Rules\ProcessedData\RegisteredBusRoute'));
            $validatorChain->attach($validatorManager->get('Rules\ProcessedData\LocalAuthorityMissing'));
            $validatorChain->attach($validatorManager->get('Rules\ProcessedData\VariationNumber'));
        }

        return $service;
    }
}
