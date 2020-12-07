<?php

namespace Dvsa\Olcs\Api\Service\Nr\Mapping;

use Olcs\XmlTools\Filter\MapXmlFile;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class ComplianceEpisodeXmlFactory
 * @package Dvsa\Olcs\Api\Service\Nr\Mapping
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ComplianceEpisodeXmlFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return ComplianceEpisodeXml
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['nr']['compliance_episode']['xmlNs'])) {
            throw new \RuntimeException('Missing INR service config');
        }

        $mapXmlFile = $serviceLocator->get('FilterManager')->get(MapXmlFile::class);

        return new ComplianceEpisodeXml($mapXmlFile, $config['nr']['compliance_episode']['xmlNs']);
    }
}
