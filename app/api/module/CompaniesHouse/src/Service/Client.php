<?php

namespace Dvsa\Olcs\CompaniesHouse\Service;

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

        /** @var $response Zend\Http\Response */
        $response = $this->getHttpClient()->send();

        if (!$response->isOk()) {
            throw new Exception('Error response: ' . $response->getBody());
        }

        return json_decode($response->getBody(), true);
    }
}
