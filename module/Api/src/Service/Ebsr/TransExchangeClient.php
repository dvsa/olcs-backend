<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Dvsa\Olcs\Api\Domain\Exception\TransxchangeException;
use Olcs\XmlTools\Filter\ParseXmlString;
use Olcs\XmlTools\Filter\MapXmlFile;
use Olcs\XmlTools\Validator\Xsd;
use Laminas\Http\Client as RestClient;
use Olcs\Logging\Log\Logger;

/**
 * Class TransExchangeClient
 * @package Olcs\Ebsr\Service
 */
class TransExchangeClient implements TransExchangeClientInterface
{
    public const REQUEST_MAP_TEMPLATE = 'RequestMap';
    public const TIMETABLE_TEMPLATE = 'Timetable';
    public const DVSA_RECORD_TEMPLATE = 'DvsaRecord';
    public const TRANSXCHANGE_INVALID_XML = 'TransXchange response did not validate against the schema: ';

    /**
     * TransExchangeClient constructor.
     *
     * @param RestClient     $restClient   Laminas rest client
     * @param MapXmlFile     $xmlFilter    olcs-xmltools xml filter
     * @param ParseXmlString $xmlParser    olcs-xmltools xml parser
     * @param Xsd            $xsdValidator olcs-xmltools xml validator
     */
    public function __construct(private RestClient $restClient, private MapXmlFile $xmlFilter, private ParseXmlString $xmlParser, private Xsd $xsdValidator)
    {
    }

    /**
     * Makes the transxchange request
     *
     * @param string $content content of the request
     *
     * @throws TransxchangeException
     * @return array
     */
    public function makeRequest($content)
    {
        Logger::info('TransXchange request', ['data' => $content]);

        $this->restClient->getRequest()->setContent($content);
        $response = $this->restClient->send();
        $body = $response->getContent();

        Logger::info('TransXchange response', ['data' => $response->toString()]);

        //security check, and parse into dom document
        $dom = $this->xmlParser->filter($body);

        //validate against schema
        if (!($this->xsdValidator->isValid($dom))) {
            $message = self::TRANSXCHANGE_INVALID_XML . implode(', ', $this->xsdValidator->getMessages());
            Logger::info('TransXchange error', ['data' => $message]);
            throw new TransxchangeException($message);
        }

        return $this->xmlFilter->filter($dom);
    }
}
