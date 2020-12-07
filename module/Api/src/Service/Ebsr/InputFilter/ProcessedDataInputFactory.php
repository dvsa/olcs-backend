<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\BusRegNotFound;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\LocalAuthorityMissing;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\NewAppAlreadyExists;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\RegisteredBusRoute;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\VariationNumber;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
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
            $validatorChain->attach($validatorManager->get(BusRegNotFound::class), true);
            $validatorChain->attach($validatorManager->get(NewAppAlreadyExists::class));
            $validatorChain->attach($validatorManager->get(RegisteredBusRoute::class));
            $validatorChain->attach($validatorManager->get(LocalAuthorityMissing::class));
            $validatorChain->attach($validatorManager->get(VariationNumber::class));
        }

        return $service;
    }
}
