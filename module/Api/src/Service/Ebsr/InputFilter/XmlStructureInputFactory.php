<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Filter\ParseXml;

/**
 * Class FileStructureInputFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr\InputFilter
 */
class XmlStructureInputFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $inputName = 'xml_structure';
        $service = new Input($inputName);
        $config = $serviceLocator->get('Config');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($serviceLocator->get('FilterManager')->get(ParseXml::class));

        $validatorchain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            $xsdValidator = $serviceLocator->get('ValidatorManager')->get(Xsd::class);
            $xsdValidator->setXsd('http://www.transxchange.org.uk/schema/2.1/TransXChange_registration.xsd');

            $validatorchain->attach($xsdValidator);
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\ServiceClassification'));
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\Operator'));
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\Registration'));
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\SupportingDocuments'));
        }

        return $service;
    }
}
