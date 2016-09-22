<?php

namespace Dvsa\Olcs\CompaniesHouse\Service;

use Dvsa\Olcs\CompaniesHouse\Service\Exception as ServiceException;
use Dvsa\Olcs\CompaniesHouse\Service\Exception\RateLimitException;
use Dvsa\Olcs\CompaniesHouse\Service\Exception\NotFoundException;
use Zend\Http\Client as HttpClient;

/**
 * Class Client
 */
class Client
{
    const ERR_KEY_COMPANY_PROFILE_NOT_FOUND = 'company-profile-not-found';

    const ERR_INVALID_JSON = 'Invalid JSON';
    const ERR_SERVICE_NOT_RESPOND = 'Service not respond';
    const ERR_COMPANY_PROFILE_NOT_FOUND = 'Company not found';
    const ERR_RATE_LIMIT_EXCEED = 'Rate limit exceeded';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * Set client to company house Api
     *
     * @param \Zend\Http\Client $httpClient Http Client to CP Api
     *
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Set base part of url for CP Api requests
     *
     * @param string $baseUri Base url for CP api requests
     *
     * @return $this
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = rtrim($baseUri, '/');
        return $this;
    }

    /**
     * Get company data and company officers data from Company House API
     *
     * @param string $companyNumber   Company number
     * @param bool   $includeOfficers True, if need also request officers
     *
     * @return array
     * @throws Exception
     */
    public function getCompanyProfile($companyNumber, $includeOfficers = true)
    {
        $companyProfile = $this->getData('/company/' . strtoupper($companyNumber));

        if (!isset($companyProfile['company_number'])) {
            throw new ServiceException(self::ERR_INVALID_JSON);
        }

        if ($includeOfficers) {
            $officers = $this->getOfficerSummary($companyNumber);
            $companyProfile['officer_summary']['officers'] = $officers;
        }

        return $companyProfile;
    }

    /**
     * Return active officers in the same format as was previously included
     * in the CompanyProfile response
     *
     * @param string $companyNumber Company no
     *
     * @return array
     */
    protected function getOfficerSummary($companyNumber)
    {
        $officers = $this->getOfficers($companyNumber);

        return array_filter(
            $officers['items'],
            function ($officer) {
                return empty($officer['resigned_on']);
            }
        );
    }

    /**
     * Get Company Officers from CP api
     *
     * @param string $companyNumber Company number
     *
     * @return array
     */
    public function getOfficers($companyNumber)
    {
        return $this->getData('/company/' . strtoupper($companyNumber) . '/officers');
    }

    /**
     * Make request to CP api
     *
     * @param string $resourcePath to be appended to baseUri
     *
     * @return array
     */
    protected function getData($resourcePath)
    {
        $uri = $this->baseUri . $resourcePath;

        $this->httpClient->getRequest()
            ->setUri($uri)
            ->setMethod('GET');

        /** @var $response \Zend\Http\Response */
        $response = $this->httpClient->send();

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $reason = null;
        $exceptionClass = null;

        if (!$response->isOk()) {
            $errors = (isset($body['errors']) ? $body['errors'] : []);

            if ($statusCode === \Zend\Http\Response::STATUS_CODE_429) {
                $reason = self::ERR_RATE_LIMIT_EXCEED;
                $exceptionClass = RateLimitException::class;

            } elseif ($statusCode === \Zend\Http\Response::STATUS_CODE_404) {
                //  set common reason and exception class
                $reason = self::ERR_SERVICE_NOT_RESPOND;
                $exceptionClass = ServiceException::class;

                //  if has errors try to raise then specific exception or reason
                if (! empty($errors)) {
                    $err = array_filter(
                        $errors,
                        function ($item) {
                            return ($item['error'] === self::ERR_KEY_COMPANY_PROFILE_NOT_FOUND);
                        }
                    );

                    if (count($err) !== 0) {
                        $reason = self::ERR_COMPANY_PROFILE_NOT_FOUND;
                        $exceptionClass = NotFoundException::class;
                    }
                }

            } else {
                $reason = $response->getBody();
                $exceptionClass = ServiceException::class;
            }

        } elseif (json_last_error() !== JSON_ERROR_NONE) {
            $reason = self::ERR_INVALID_JSON;
            $exceptionClass = ServiceException::class;
        }

        if ($exceptionClass !== null) {
            $message = sprintf('Error response (%s) %s', $response->getStatusCode(), $reason);

            throw new $exceptionClass($message);
        }

        return $body;
    }
}
