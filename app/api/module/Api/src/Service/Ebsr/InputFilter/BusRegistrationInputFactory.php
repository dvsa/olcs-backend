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

        /** @var MapXmlFile $mapXmlFile */
        $mapXmlFile = $serviceLocator->get('FilterManager')->get(MapXmlFile::class);
        $mapXmlFile->setMapping($serviceLocator->get('TransExchangeXmlMapping'));

        $filterChain = $service->getFilterChain();
        $filterChain->attach($mapXmlFile);
        $filterChain->attach($serviceLocator->get('FilterManager')->get('InjectIsTxcApp'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('InjectReceivedDate'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('InjectNaptanCodes'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('IsScottishRules'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('Format\Subsidy'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('Format\Via'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('Format\ExistingRegNo'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get(MiscSnJustification::class));

        $validatorChain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\EffectiveDate'));
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ApplicationType'));
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\Licence'));
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get(ServiceNo::class));
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get(EndDate::class));
        }

        return $service;
    }
}
