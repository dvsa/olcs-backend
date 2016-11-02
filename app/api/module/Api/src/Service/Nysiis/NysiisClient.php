<?php

namespace Dvsa\Olcs\Api\Service\Nysiis;

use Zend\Soap\Client as ZendSoap;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Olcs\Logging\Log\Logger;

/**
 * Class NysiisClient
 */
class NysiisClient
{
    /**
     * @var ZendSoap
     */
    private $soapClient;

    /**
     * Nysiis client constructor
     *
     * @param ZendSoap $soapClient Zend soap client
     *
     * @return $this
     */
    public function __construct(ZendSoap $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * Fetch Nysiis data
     *
     * @param string $forename   input forename
     * @param string $familyName input family name
     *
     * @return array
     * @throws NysiisException
     */
    public function makeRequest($forename, $familyName)
    {
        Logger::info('Nysiis parameters', ['data' => [$forename, $familyName]]);

        try {
            $nysiisData = $this->soapClient->GetNYSIISSearchKeys(
                [
                    'firstName' => $forename,
                    'familyName' => $familyName
                ]
            );
        } catch (\SoapFault $e) {
            // Catch SoapFault exceptions and ensure a Nysiis exception is thrown to trigger a requeue
            $this->logClientInfo();
            Logger::info('Nysiis soap fault object', ['data' => $e->__toString()]);
            throw new NysiisException('SOAP Fault connecting to Nysiis service: ' . $e->getMessage());
        }

        $this->logClientInfo();
        Logger::info('Nysiis returned', ['data' => [$nysiisData->FirstName(), $nysiisData->FamilyName()]]);

        return [
            'forename' => $nysiisData->FirstName(),
            'familyName' => $nysiisData->FamilyName()
        ];

    }

    /**
     * Logs information from the client
     *
     * @return void
     */
    private function logClientInfo()
    {
        Logger::info('Nysiis available functions', ['data' => $this->soapClient->getFunctions()]);
        Logger::info('Nysiis xml request headers', ['data' => $this->soapClient->getLastRequestHeaders()]);
        Logger::info('Nysiis xml request body', ['data' => $this->soapClient->getLastRequest()]);
        Logger::info('Nysiis xml response headers', ['data' => $this->soapClient->getLastResponseHeaders()]);
        Logger::info('Nysiis xml response body', ['data' => $this->soapClient->getLastResponse()]);
    }
}
