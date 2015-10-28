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
        $service = new Input('dom_document');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($serviceLocator->get('FilterManager')->get(ParseXml::class));

        $xsdValidator = $serviceLocator->get('ValidatorManager')->get(Xsd::class);
        $xsdValidator->setXsd('http://www.transxchange.org.uk/schema/2.1/TransXChange_registration.xsd');

        $validatorchain = $service->getValidatorChain();
        $validatorchain->attach($xsdValidator);
        $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\ServiceClassification'));
        $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\Operator'));
        $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\Registration'));
        $validatorchain->attach($serviceLocator->get('ValidatorManager')->get('Structure\SupportingDocuments'));

        return $service;
    }
}
