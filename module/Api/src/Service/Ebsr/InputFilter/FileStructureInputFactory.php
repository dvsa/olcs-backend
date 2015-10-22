<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FileStructureInputFactory
 * @package Olcs\Ebsr\InputFilter
 */
class FileStructureInputFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Input('xml_filename');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($serviceLocator->get('FilterManager')->get('DecompressToTmp'));
        $filterChain->attach($serviceLocator->get('FilterManager')->get('XmlFromDir'));

        return $service;
    }
}
