<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\MiscSnJustification;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ServiceNo;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\EndDate;

/**
 * Class BusRegistrationInputFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr\InputFilter
 */
class BusRegistrationInputFactory implements FactoryInterface
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
        $inputName = 'bus_registration';
        $service = new Input($inputName);
        $config = $serviceLocator->get('Config');

        /** @var ServiceLocatorInterface $filterManager */
        $filterManager = $serviceLocator->get('FilterManager');

        /** @var MapXmlFile $mapXmlFile */
        $mapXmlFile = $filterManager->get(MapXmlFile::class);
        $mapXmlFile->setMapping($serviceLocator->get('TransExchangeXmlMapping'));

        $filterChain = $service->getFilterChain();
        $filterChain->attach($mapXmlFile);
        $filterChain->attach($filterManager->get('InjectIsTxcApp'));
        $filterChain->attach($filterManager->get('InjectReceivedDate'));
        $filterChain->attach($filterManager->get('InjectNaptanCodes'));
        $filterChain->attach($filterManager->get('IsScottishRules'));
        $filterChain->attach($filterManager->get('Format\Subsidy'));
        $filterChain->attach($filterManager->get('Format\Via'));
        $filterChain->attach($filterManager->get('Format\ExistingRegNo'));
        $filterChain->attach($filterManager->get(MiscSnJustification::class));

        $validatorChain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            /** @var ServiceLocatorInterface $validatorManager */
            $validatorManager = $serviceLocator->get('ValidatorManager');
            $validatorChain->attach($validatorManager->get('Rules\EffectiveDate'));
            $validatorChain->attach($validatorManager->get('Rules\ApplicationType'));
            $validatorChain->attach($validatorManager->get('Rules\Licence'));
            $validatorChain->attach($validatorManager->get(ServiceNo::class));
            $validatorChain->attach($validatorManager->get(EndDate::class));
        }

        return $service;
    }
}
