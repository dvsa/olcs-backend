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
        $xmlBuilder = new XmlNodeBuilder('MS2ERRU_Infringement_Res', 'https://webgate.ec.testa.eu/erru/1.0', []);

        return new MsiResponse($xmlBuilder);
    }
}
