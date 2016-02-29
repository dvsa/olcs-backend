<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Filter\ParseXmlString;
use Dvsa\Olcs\Api\Service\InputFilter\Input;

/**
 * Class XmlStructureInputFactory
 * @package Dvsa\Olcs\Api\Service\Nr\InputFilter
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
        $service = new Input('xml_structure');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($serviceLocator->get('FilterManager')->get(ParseXmlString::class));

        $validatorChain = $service->getValidatorChain();

        $xsdValidator = $serviceLocator->get('ValidatorManager')->get(Xsd::class);
        $xsdValidator->setXsd('https://webgate.ec.testa.eu/erru/1.0');
        $validatorChain->attach($xsdValidator);

        return $service;
    }
}
