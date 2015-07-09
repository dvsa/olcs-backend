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
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

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
     * Sets the value of username.
     *
     * @param string $username the username
     *
     * @return self
     */
    protected function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the value of username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the value of password.
     *
     * @param string $password the password
     *
     * @return self
     */
    protected function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the value of password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
        // @TODO combine officer data with default response

        return $this->getData('/company/' . $companyNumber);
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
    protected function getData($resourcePath) {
        $uri = $this->getBaseUri() . $resourcePath;

        $this->getHttpClient()->getRequest()
            ->setUri($uri)
            ->setMethod('GET');

        $response = $this->getHttpClient()->send();

        $jsonResponse = json_decode($response->getBody(), true);

        return $jsonResponse;
    }
}
