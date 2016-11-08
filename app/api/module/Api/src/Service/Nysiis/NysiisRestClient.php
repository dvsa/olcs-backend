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

        $this->restClient->setEncType('application/json');
        $this->restClient->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->restClient->getRequest()->setContent(ZendJson::encode($inputData));

        try {
            $response = $this->restClient->send();
            Logger::info('Nysiis response', ['data' => $response]);

            if ($response instanceof HttpResponse && $response->isSuccess()) {
                return ZendJson::decode($response->getContent(), ZendJson::TYPE_ARRAY);
            }
        } catch (\Exception $e) {
            Logger::info('Nysiis exception object', ['data' => $e->__toString()]);
            throw new NysiisException(sprintf(self::NYSIIS_FAILURE, $e->getMessage()));
        }

        throw new NysiisException('Nysiis REST service returned incorrect response');
    }
}
