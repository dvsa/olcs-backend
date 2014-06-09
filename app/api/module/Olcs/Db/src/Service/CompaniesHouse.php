<?php

/**
 * Companies House Request Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service;

use Olcs\Db\Exceptions\RestResponseException;
use Zend\Http\Response;
use Olcs\Db\Service\CHXmlGateway\lib\CHXmlGateway;

/**
 * Companies House Request Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompaniesHouse extends ServiceAbstract
{

    /**
     * Default number of rows to return
     */
    const DEFAULT_SEARCH_ROWS = 10;

    /**
     * Transaction Id for Companies House Request
     */
    private $transactionId;

    /**
     * Password to the Companies House API
     */
    private $password;

    /**
     * User ID for the Companies House API
     */
    private $userId;

    /**
     * Returns a list of records after Companies House API's call
     *
     * @param array $data array with params
     * @return array
     */
    public function getList($data)
    {
        // Didn't want to change this logic, but I think this should return an error
        if (!array_key_exists('type', $data) || !array_key_exists('value', $data)) {
            return array('Count' => 0, 'Results' => array());
        }

        list($requestType, $method) = $this->getRequestType($data);

        $this->setCredentials();

        $this->setTransactionId($this->getService('CompaniesHouseRequest')->initiateRequest($requestType)->getId());

        try {
            $result = $this->$method($this->getNewGateway(), $data['value']);
        } catch (\Exception $ex) {
            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        $finalResult = $this->getFinalResult($result);

        return array('Count' => count($finalResult), 'Results' => $finalResult);
    }

    /**
     * Get a new instance of gateway
     *
     * @return \Olcs\Db\Service\CHXmlGateway\lib\CHXmlGateway
     */
    public function getNewGateway()
    {
        $gateway = new CHXmlGateway();
        $gateway->setPassword($this->getPassword());
        $gateway->setUserId($this->getUserId());

        return $gateway;
    }

    /**
     * Search Companies House for a company by name
     *
     * @param object $gateway
     * @param string $companyName Company name to search for
     * @param string $companyStatus status of companies to search for. Options
     *               are 'LIVE', 'FORMER', 'DISOLVED', 'PROPOSED'. Default is 'LIVE'
     * @return array
     */
    protected function nameSearch($gateway, $companyName, $companyStatus = 'LIVE')
    {
        $request = $this->setRequestRows($gateway->getNameSearch($companyName, $companyStatus));

        return $this->handleResponse($gateway, $request);
    }

    /**
     * Searches Companies House API for a partial or whole company number
     *
     * @param object $gateway
     * @param string $companyNumber string containing the search parameter
     * @param array $companyStatus array of company status's to search for
     * @return array
     */
    protected function numberSearch($gateway, $companyNumber, $companyStatus = array('LIVE'))
    {
        $request = $this->setRequestRows($gateway->getNumberSearch($companyNumber, $companyStatus));

        return $this->handleResponse($gateway, $request);
    }

    /**
     * Searches Companies House API for company details given a company number
     *
     * @param object $gateway
     * @param string $companyNumber string containing the search parameter
     * @return array
     */
    protected function companyDetails($gateway, $companyNumber)
    {
        $request = $gateway->getCompanyDetails($companyNumber);

        return $this->handleResponse($gateway, $request);
    }

    /**
     * detectError
     *
     * Checks for error and if there is any,
     * then returns them. This overrides the equivalent function provided originally
     * that wrote the error to the database
     *
     * @param \SimpleXMLElement $xmlObject $response
     * @access private
     * @return object
     */
    private function detectError(\SimpleXMLElement $xmlObject)
    {
        if (isset($xmlObject->GovTalkDetails->GovTalkErrors->Error)) {
            return $xmlObject->GovTalkDetails->GovTalkErrors;
        }

        return null;
    }

    /**
     * Sets number of request's rows
     *
     * @param Request $request
     * @return Request
     */
    private function setRequestRows($request)
    {
        $request->setSearchRows(self::DEFAULT_SEARCH_ROWS);

        return $request;
    }

    /**
     * Set credentials
     */
    private function setCredentials()
    {
        $config = $this->getServiceLocator()->get('Config');

        $this->setPassword($config['companies_house_credentials']['password']);
        $this->setUserId($config['companies_house_credentials']['userId']);
    }

    /**
     * Get final result from result
     *
     * @param object $result
     * @return array
     */
    private function getFinalResult($result)
    {
        $finalResult = array();

        if (array_key_exists('NameSearch', $result)) {

            foreach ($result->NameSearch->CoSearchItem as $item) {
                $finalResult[] = $item;
            }

        } elseif (array_key_exists('NumberSearch', $result)) {

            foreach ($result->NumberSearch->CoSearchItem as $item) {
                $finalResult[] = $item;
            }

        } elseif (array_key_exists('CompanyDetails', $result)) {

            foreach ($result->CompanyDetails as $item) {
                $finalResult[] = $item;
            }
        }

        return $finalResult;
    }

    /**
     * Get request type and method based on the data
     *
     * @param array $data
     * @return array
     * @throws RestResponseException
     */
    private function getRequestType($data)
    {
        if (!in_array($data['type'], array('nameSearch', 'numberSearch', 'companyDetails'))) {
            throw new RestResponseException('Wrong request type - ' . $data['type'], Response::STATUS_CODE_500);
        }

        $requestType = $method = $data['type'];

        if ($data['type'] == 'companyDetails') {
            $requestType = 'numberSearch';
        }

        return array($requestType, $method);
    }

    /**
     * Handle response
     *
     * @param object $gateway
     * @param object $request
     * @return array
     */
    private function handleResponse($gateway, $request)
    {
        $xml = $gateway->getResponse($request, $this->getTransactionId());

        $xmlObject = simplexml_load_string($xml);

        $error = $this->detectError($xmlObject);

        if (!is_null($error)) {
            $return = $error;
        } elseif (isset($xmlObject->Body)) {
            $return = $xmlObject->Body;
        } else {
            $return = array();
        }

        if (isset($return->Error) && $return->Error->Number != 600) {
            throw new \Exception(
                'Companies house API error: Number - ' . $return->Error->Number
                . ' type: ' . $return->Error->Type
                . ' text: ' . $return->Error->Text, Response::STATUS_CODE_500
            );
        }

        return $return;
    }

    /**
     * Gets transation ID
     *
     * @return int
     */
    private function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Gets transation Id
     *
     * @return int
     */
    private function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Gets password
     *
     * @return string
     */
    private function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets password
     *
     * @param string $password
     */
    private function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Gets User ID
     *
     * @return string $userId
     */
    private function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets User ID
     *
     * @param string $userId
     */
    private function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
