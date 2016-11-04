<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Nr\Filter\LicenceNumber;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\MemberStateCode;
use Dvsa\Olcs\Api\Service\Nr\Filter\Vrm as VrmFilter;
use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $fm = $serviceLocator->get('FilterManager');

        $service = new Input('compliance_episode');

        /** @var \Olcs\XmlTools\Filter\MapXmlFile $mapXmlFile */
        $mapXmlFile = $fm->get(MapXmlFile::class);
        $mapXmlFile->setMapping($serviceLocator->get('ComplianceEpisodeXmlMapping'));

        $filterChain = $service->getFilterChain();
        $filterChain
            ->attach($mapXmlFile)
            ->attach($fm->get(VrmFilter::class))
            ->attach($fm->get(LicenceNumber::class))
            ->attach($fm->get(MemberStateCode::class));

        return $service;
    }
}
