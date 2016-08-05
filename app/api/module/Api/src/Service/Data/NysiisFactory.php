<?php

namespace Dvsa\Olcs\Api\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Log\Logger;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;

/**
 * Class NysiisFactory
 * @package Olcs\Service\Data
 */
class NysiisFactory implements FactoryInterface
{
    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Nysiis
     * @throws NysiisException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /*
           Due to a bug in the PHP SOAP library a PHP Fatal is generated instead of a SoapFault Exception in the
           event that the library cannot connect to a server (or has invalid WSDL). To get around this, there is a
           temporary error handler in place to ensure that a SoapFault is generated.
           Error reporting has also been switched off to prevent the error_get_last() returning this error, as part of
           the public/index.php script
         */

        $config = $serviceLocator->get('Config');

        if (!isset($config['nysiis']['wsdl']['uri'])) {
            throw new NysiisException('Unable to create soap client: WSDL not found');
        }

        $soapClient = $this->generateSoapClient($config);

        $nysiisService = new Nysiis($soapClient, $config);

        return $nysiisService;
    }

    /**
     * Method to generate the SOAP client.
     *
     * @param   array       $config
     * @return  \SoapClient
     * @throws  \SoapFault
     * @throws  \Exception
     */
    protected function generateSoapClient($config)
    {
        // set error handler to combat Fatal error generated by SOAP client
        set_error_handler([$this, 'nysiisCustomSoapWsdlErrorHandler']);
        $errorReportingLevel = ini_get('error_reporting');

        // suppress Fatal error generated when queue shuts down
        error_reporting(0);

        $soapClient = @new \SoapClient(
            $config['nysiis']['wsdl']['uri'],
            $config['nysiis']['wsdl']['soap']['options']
        );
        // restore original error_reporting level
        error_reporting($errorReportingLevel);

        restore_error_handler();

        return $soapClient;
    }
    /**
     * Error handler for instantiation of SOAP client. This is necessary due to PHP Fatal errors being generated when
     * the service is down and no connection to Nysiis is available.
     * This error handler ensures that a PHP SoapFault exception is raised rather than a PHP Fatal error. The
     * exception can then be caught and handled correctly.
     * Fixes possible bug with WSDL and SOAP.
     * @see http://php.net/manual/en/class.soapclient.php
     * @see https://bugs.php.net/bug.php?id=47584
     * 
     * @param   int         $errno
     * @param   string      $errstr
     * @param   null|string $errfile
     * @param   null|int    $errline
     * @param   null|string $errcontext
     * @throws \SoapFault
     */
    public function nysiisCustomSoapWsdlErrorHandler(
        $errno,
        $errstr,
        $errfile = null,
        $errline = null,
        $errcontext = null
    ) {
        // Simulate the exception that Soap *should* have thrown instead of an error.
        // This is needed to support certain server setups it would seem.
        $wsdl_url = isset($errcontext['wsdl']) ? $errcontext['wsdl'] : '';
        $msg = "SOAP-ERROR: Parsing WSDL: Couldn't load from '" . $wsdl_url .
            "' : failed to load external entity \"" . $wsdl_url . "\"";
        if (class_exists('SoapFault')) {
            $e = new \SoapFault('WSDL', $msg);
        } else {
            $e = new \Exception($msg, 0);
        }
        throw $e;
    }
}
