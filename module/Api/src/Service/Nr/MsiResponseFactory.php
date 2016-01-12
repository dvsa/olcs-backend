<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Olcs\XmlTools\Xml\XmlNodeBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MsiResponseFactory
 * @package Dvsa\Olcs\Api\Service\Nr
 */
class MsiResponseFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['nr']['msi_response'])) {
            throw new \RuntimeException('Missing MSI response config');
        }

        $config = $config['nr']['msi_response'];

        $xmlBuilder = new XmlNodeBuilder($config['parent_node'], $config['ns'], []);

        return new MsiResponse($xmlBuilder);
    }
}
