<?php

namespace Dvsa\Olcs\Api\Service\Nr\Mapping;

use Interop\Container\ContainerInterface;
use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        if (!isset($config['nr']['compliance_episode']['xmlNs'])) {
            throw new \RuntimeException('Missing INR service config');
        }

        $mapXmlFile = $container->get('FilterManager')->get(MapXmlFile::class);

        return new ComplianceEpisodeXml($mapXmlFile, $config['nr']['compliance_episode']['xmlNs']);
    }
}
