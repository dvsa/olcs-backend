<?php

namespace Dvsa\Olcs\Api\Service\Nysiis;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Zend\Soap\Client as ZendSoap;

/**
 * Class NysiisClientFactory
 * @package Dvsa\Olcs\Api\Service\Nysiis
 */
class NysiisSoapClientFactory implements FactoryInterface
{
    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return NysiisClient
     * @throws NysiisException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['nysiis']['wsdl'])) {
            throw new NysiisException('No NYSIIS Wsdl file specified in config');
        }

        if (!isset($config['nysiis']['options'])) {
            throw new NysiisException('No NYSIIS options specified in config');
        }

        /**
         * TLS 1 compatibility, so we can work with ATOS
         * http://phil.lavin.me.uk/2014/04/how-to-force-tls-v1-0-in-php/
         * http://docs.php.net/manual/en/migration56.openssl.php#migration56.openssl.crypto-method
         */
        $context = stream_context_create(
            [
                'ssl' => [
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT,
                ],
            ]
        );

        $config['nysiis']['options']['stream_context'] = $context;

        //instantiate soap client (checks php soap service is loaded)
        try {
            $soapClient = new ZendSoap($config['nysiis']['wsdl'], $config['nysiis']['options']);
        } catch (\Exception $e) {
            throw new NysiisException($e->getMessage());
        }

        return new NysiisClient($soapClient);
    }
}
