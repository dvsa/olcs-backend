<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Filter\ParseXml;
use Dvsa\Olcs\Api\Service\InputFilter\Input;

/**
 * Class XmlStructureInputFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr\InputFilter
 */
class XmlStructureInputFactory implements FactoryInterface
{
    const MAX_SCHEMA_MSG = 'No config specified for max_schema_errors';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return Input
     * @throws \RuntimeException
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
            if (!isset($config['ebsr']['max_schema_errors'])) {
                throw new \RuntimeException(self::MAX_SCHEMA_MSG);
            }

            /** @var Xsd $xsdValidator */
            $xsdValidator = $serviceLocator->get('ValidatorManager')->get(Xsd::class);
            $xsdValidator->setXsd('http://www.transxchange.org.uk/schema/2.1/TransXChange_registration.xsd');
            $xsdValidator->setMaxErrors($config['ebsr']['max_schema_errors']);

            $validatorchain->attach($xsdValidator);
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\ServiceClassification'));
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\Operator'));
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\Registration'));
            $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\SupportingDocuments'));
        }

        return $service;
    }
}
