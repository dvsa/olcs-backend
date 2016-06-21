<?php

namespace Dvsa\Olcs\Api\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Soap\Client as SoapClient;
use Olcs\Logging\Log\Logger;

/**
 * Class NysiisFactory
 * @package Olcs\Service\Data
 */
class NysiisFactory implements FactoryInterface
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

        $soapClient = false;

        try {
            if (file_exists($config['nysiis']['wsdl']['uri'])) {
                $wsdl = file_get_contents($config['nysiis']['wsdl']['uri']);

                $soapClient = new SoapClient(
                    $wsdl,
                    $config['nysiis']['wsdl']['soap']['options']
                );
            } else {
                throw new \Exception('WSDL file not found');
            }

        } catch (\Exception $e) {
            Logger::debug(__FILE__ . 'Unable to create soap client: ' . $e->getMessage());
        }

        return new Nysiis($soapClient, $config);
    }
}
