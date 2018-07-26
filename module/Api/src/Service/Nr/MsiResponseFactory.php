<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Interop\Container\ContainerInterface;
use Olcs\XmlTools\Xml\XmlNodeBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        if (!isset($config['nr']['compliance_episode']['xmlNs'])) {
            throw new \RuntimeException(self::XML_NS_MSG);
        }

        $xmlBuilder = new XmlNodeBuilder('MS2ERRU_Infringement_Res', $config['nr']['compliance_episode']['xmlNs'], []);

        return new MsiResponse($xmlBuilder);
    }
}
