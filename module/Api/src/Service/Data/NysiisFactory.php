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
            $soapClient = new SoapClient(
                $config['nysiis']['wsdl']['uri'],
                array_merge(
                    $config['nysiis']['wsdl']['soap']['options'],
                    []
                )
            );
        } catch (\Exception $e) {
            throw new NysiisException('Unable to create soap client: ' . $e->getMessage());
        }

        return new Nysiis($soapClient, $config);
    }
}
