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
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @param \Zend\Http\Client $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string $baseUri
     * @return $this
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = rtrim($baseUri, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param string $companyNumber
     * @param boolean $includeOfficers include officer_summary which was there
     * by default in earlier versions of the api, however we now have to make a
     * separate REST call to include it :(
     * @link http://forum.aws.chdev.org/t/company-profile/136
     *
     * @return array
     */
    public function getCompanyProfile($companyNumber, $includeOfficers = true)
    {
        $companyProfile = $this->getData('/company/' . $companyNumber);

        if ($includeOfficers) {
            $officers = $this->getOfficerSummary($companyNumber);
            $companyProfile['officer_summary']['officers'] = $officers;
        }

        return $companyProfile;
    }

    /**
     * Return active officers in the same format as was previously included
     * in the CompanyProfile response
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
     * @param string $companyNumber
     * @return array
     */
    public function getOfficers($companyNumber)
    {
        return $this->getData('/company/' . $companyNumber . '/officers');
    }

    /**
     * @param string $resourcePath to be appended to baseUri
     * @return array
     */
    protected function getData($resourcePath)
    {
        $uri = $this->getBaseUri() . $resourcePath;

        $this->getHttpClient()->getRequest()
            ->setUri($uri)
            ->setMethod('GET');

        /** @var $response \Zend\Http\Response */
        $response = $this->getHttpClient()->send();

        if (!$response->isOk()) {

            if ($response->getStatusCode() === \Zend\Http\Response::STATUS_CODE_429) {
                $reason = 'Rate limit exceeded';
                $exceptionClass = RateLimitException::class;
            } elseif ($response->getStatusCode() === \Zend\Http\Response::STATUS_CODE_404) {
                $reason = 'Company not found';
                $exceptionClass = NotFoundException::class;
            } else {
                $reason = $response->getBody();
                $exceptionClass = ServiceException::class;
            }

            $message = sprintf('Error response (%s) %s', $response->getStatusCode(), $reason);

            throw new $exceptionClass($message);
        }

        return json_decode($response->getBody(), true);
    }
}
