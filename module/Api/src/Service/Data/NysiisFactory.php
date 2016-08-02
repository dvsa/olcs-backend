<?php

namespace Dvsa\Olcs\Api\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Soap\Client as SoapClient;
use Olcs\Logging\Log\Logger;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;

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

        if (!isset($config['nysiis']['wsdl']['uri'])) {
            throw new NysiisException('Unable to create soap client: WSDL not found');
        }

        try {
            /**
             * This is requesting the wsdl twice because setting the SOAP client directly causes PHP Fatal error,
             * when service is down. When the service is up, reusing the $wsdl string by calling setWSDL($wsdl); also
             * causes a Fatal error - unable to parse document.
             */
            $wsdl = file_get_contents($config['nysiis']['wsdl']['uri']);

            if (!empty($wsdl)) {
                $soapClient = new SoapClient($config['nysiis']['wsdl']['uri']);
                $soapClient->setOptions(
                    array_merge(
                        $config['nysiis']['wsdl']['soap']['options'],
                        []
                    )
                );
                return new Nysiis($soapClient, $config);
            }
        } catch (\SoapFault $e) {
            throw new NysiisException('Unable to create soap client: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new NysiisException('Unable to create soap client: ' . $e->getMessage());
        }
    }
}
