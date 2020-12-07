<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Olcs\XmlTools\Xml\XmlNodeBuilder;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class MsiResponseFactory
 * @package Dvsa\Olcs\Api\Service\Nr
 */
class MsiResponseFactory implements FactoryInterface
{
    const XML_NS_MSG = 'No config specified for xml ns';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return MsiResponse
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['nr']['compliance_episode']['xmlNs'])) {
            throw new \RuntimeException(self::XML_NS_MSG);
        }

        $xmlBuilder = new XmlNodeBuilder('MS2ERRU_Infringement_Res', $config['nr']['compliance_episode']['xmlNs'], []);

        return new MsiResponse($xmlBuilder);
    }
}
