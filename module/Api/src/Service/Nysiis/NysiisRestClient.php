<?php

namespace Dvsa\Olcs\Api\Service\Nysiis;

use Zend\Http\Client as RestClient;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Zend\Json\Json as ZendJson;
use Olcs\Logging\Log\Logger;

/**
 * Class NysiisRestClient
 */
class NysiisRestClient
{
    const NYSIIS_FAILURE = 'Nysiis REST service failure: %s';
    const NYSIIS_RESPONSE_INCORRECT = 'Nysiis REST service returned incorrect response';

    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * Nysiis client constructor
     *
     * @param RestClient $restClient Zend rest client
     *
     * @return void
     */
    public function __construct(RestClient $restClient)
    {
        $this->restClient = $restClient;
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

        $inputData = [
            'volFirstName' => $forename,
            'volFamilyName' => $familyName
        ];

        $this->restClient->setEncType('application/json; charset=UTF-8');
        $this->restClient->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->restClient->getRequest()->setContent(ZendJson::encode($inputData));

        try {
            $response = $this->restClient->send();
            Logger::info('Nysiis response', ['data' => $response]);

            if ($response instanceof HttpResponse && $response->isSuccess()) {
                return ZendJson::decode($this->cleanJson($response->getContent()), ZendJson::TYPE_ARRAY);
            }
        } catch (\Exception $e) {
            Logger::info('Nysiis exception object', ['data' => $e->__toString()]);
            throw new NysiisException(sprintf(self::NYSIIS_FAILURE, $e->getMessage()));
        }

        throw new NysiisException('Nysiis REST service returned incorrect response');
    }

    /**
     * quick fix: remove byte order mark characters from the json string
     *
     * @param string $text text being cleaned
     *
     * @todo we need to sort this problem properly, either at source (java service) or here with a more detailed fix
     *
     * @return string
     */
    public function cleanJson($text)
    {
        //if there isn't a character matched then our string is invalid, but we can't do anything about it
        if (strstr($text, '{') !== false) {
            $text = strstr($text, '{');
        }

        //find the position of the last "}" character
        $lastChar = strrpos($text, '}');

        //if there isn't a character matched then our string is invalid, but we can't do anything about it
        if ($lastChar !== false) {
            //count is from zero, so we return up to matched character + 1
            $text = substr($text, 0, $lastChar + 1);
        }

        return $text;
    }
}
