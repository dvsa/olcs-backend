<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Olcs\XmlTools\Filter\MapXmlFile;
use Dvsa\Olcs\Api\Service\Nr\Filter\LicenceNumber;
use Dvsa\Olcs\Api\Service\Nr\Filter\Vrm as VrmFilter;

/**
 * Class ComplianceEpisodeInputFactory
 * @package Dvsa\Olcs\Api\Service\Nr\InputFilter
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ComplianceEpisodeInputFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Input('compliance_episode');

        /** @var \Olcs\XmlTools\Filter\MapXmlFile $mapXmlFile */
        $mapXmlFile = $serviceLocator->get('FilterManager')->get(MapXmlFile::class);
        $mapXmlFile->setMapping($serviceLocator->get('ComplianceEpisodeXmlMapping'));

        $filterChain = $service->getFilterChain();
        $filterChain->attach($mapXmlFile);
        $filterChain->attach($serviceLocator->get('FilterManager')->get(VrmFilter::class));
        $filterChain->attach($serviceLocator->get('FilterManager')->get(LicenceNumber::class));

        return $service;
    }
}
