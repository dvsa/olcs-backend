<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Validator\Xsd;
use Olcs\XmlTools\Filter\ParseXmlString;
use Dvsa\Olcs\Api\Service\InputFilter\Input;

/**
 * Class XmlStructureInputFactory
 * @package Dvsa\Olcs\Api\Service\Nr\InputFilter
 */
class XmlStructureInputFactory implements FactoryInterface
{
    const MAX_SCHEMA_MSG = 'No config specified for max_schema_errors';
    const XML_VALID_EXCLUDE_MSG = 'No config specified for xml messages to exclude';
    const XML_NS_MSG = 'No config specified for xml ns';

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
        $config = $serviceLocator->get('Config');

        if (!isset($config['nr']['compliance_episode']['xmlNs'])) {
            throw new \RuntimeException(self::XML_NS_MSG);
        }

        if (!isset($config['nr']['max_schema_errors'])) {
            throw new \RuntimeException(self::MAX_SCHEMA_MSG);
        }

        if (!isset($config['xml_valid_message_exclude'])) {
            throw new \RuntimeException(self::XML_VALID_EXCLUDE_MSG);
        }

        $service = new Input('xml_structure');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($serviceLocator->get('FilterManager')->get(ParseXmlString::class));

        $validatorChain = $service->getValidatorChain();

        /** @var Xsd $xsdValidator */
        $xsdValidator = $serviceLocator->get('ValidatorManager')->get(Xsd::class);
        $xsdValidator->setXsd($config['nr']['compliance_episode']['xmlNs']);
        $xsdValidator->setMaxErrors($config['nr']['max_schema_errors']);
        $xsdValidator->setXmlMessageExclude($config['xml_valid_message_exclude']);
        $validatorChain->attach($xsdValidator);

        return $service;
    }
}
