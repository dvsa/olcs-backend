<?php

/**
 * Companies House Request Service
 * Counts requests to companies house API 
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Olcs\Db\Service;

// for error codes
use OlcsCommon\Controller\AbstractRestfulController as AbstractRestfulController;
use Olcs\Db\Exceptions\RestResponseException;
use Zend\Json\Json as Json;
use Zend\Http\Response;
use Olcs\Db\Service\CHXmlGateway\lib\CHXmlGateway;

/**
 * Companies House Service
 */
class CompaniesHouse extends ServiceAbstract
{

    /**
     * Default number of rows to return
     */
    const DEFAULT_SEARCH_ROWS = 10;

    /**
     * Transaction ID for Companies House Request
     */
    private $transactionID;

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
        $config = $this->getServiceLocator()->get('Config');
        $password = $config['companies_house_credentials']['password'];
        $userId = $config['companies_house_credentials']['userId'];
        $this->setPassword($password);
        $this->setUserId($userId);

        $results = array('Count' => 0, 'Results' => array());
        if (array_key_exists('type', $data) && array_key_exists('value', $data)) {
            switch ($data['type']) {
                case 'nameSearch':
                    $requestType = 'nameSearch';
                    $method      = 'nameSearch';
                    $status      = 'LIVE';
                    $options     = array('searchRows' => 10);
                    break;
                case 'numberSearch':
                    $requestType = 'numberSearch';
                    $method      = 'numberSearch';
                    $status      = array('LIVE');
                    $options     = array('searchRows' => 10);
                    break;
                case 'companyDetails':
                    $requestType = 'numberSearch';
                    $method      = 'companyDetails';
                    $status      = null;
                    $options     = null;
                    break;
                default:
                    throw new RestResponseException('Wrong request type - ' . $data['type'], Response::STATUS_CODE_500);
            }
            // get new transaction ID
            $companiesHouseRequest = $this->getService('CompaniesHouseRequest')->initiateRequest($requestType);
            $transactionId = $companiesHouseRequest->getId();

            // pass transaction ID to Companies House Service
            $this->setTransactionID($transactionId);

            // make request
            try {
                $result = $this->$method($data['value'], $status, array('searchRows' => 10));
            } catch (\Exception $ex) {
                throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
            }

            if (isset($result->Error)) {
                throw new RestResponseException('Companies house API error', Response::STATUS_CODE_500);
            }
            $finalResult = array();
            if (array_key_exists('NameSearch', $result)) {
                // converting simpleXMLelement to array
                foreach ($result->NameSearch->CoSearchItem as $item) {
                    $finalResult[] = $item;
                }
            } elseif (array_key_exists('NumberSearch', $result)) {
                // converting simpleXMLelement to array
                foreach ($result->NumberSearch->CoSearchItem as $item) {
                    $finalResult[] = $item;
                }
            } elseif (array_key_exists('CompanyDetails', $result)) {
                // converting simpleXMLelement to array
                foreach ($result->CompanyDetails as $item) {
                    $finalResult[] = $item;
                }
            }

            $results = array(
                'Count'   => count($finalResult),
                'Results' => $finalResult
            );
        }
        return $results;
    }

    /**
     * Search Companies House for a company by name
     * 
     * @param string $companyName Company name to search for
     * @param string $companyStatus status of companies to search for. Options 
     *               are 'LIVE', 'FORMER', 'DISOLVED', 'PROPOSED'. Default is 'LIVE'
     * @param array $options array of options, at present only searchRows is 
     *              processed. If not passed, default is 10 rows
     * @return array
     */
    public function nameSearch($companyName, $companyStatus = 'LIVE', $options = array())
    {
        $gateway = new CHXmlGateway();
        $gateway->setPassword($this->getPassword());
        $gateway->setUserId($this->getUserId());
        $request = $gateway->getNameSearch($companyName, $companyStatus);

        // set the number of rows required ('searchRows' in $options), default to 10
        $request = $this->setRequestRows($request, $options);

        // get response as XML
        $xml = $gateway->getResponse($request, $this->getTransactionId());

        // load XML to object
        $xmlObjects = simplexml_load_string($xml);

        // return results
        return $this->handleReturnData($xmlObjects);
    }

    /**
     * Searches Companies House API for a partial or whole company number
     * 
     * @param string $partialCompanyNumber string containing the search parameter
     * @param array $companyStatus array of company status's to search for
     * @param array $options array of options, at present only searchRows is processed
     * @return array
     */
    public function numberSearch(
        $partialCompanyNumber,
        $companyStatus = array('LIVE', 'FORMER', 'DISOLVED', 'PROPOSED'),
        $options = array()
    ) {

        $gateway = new CHXmlGateway();
        $gateway->setPassword($this->getPassword());
        $gateway->setUserId($this->getUserId());

        $request = $gateway->getNumberSearch($partialCompanyNumber, $companyStatus);

        // set the number of rows required ('searchRows' in $options), default to 10
        $request = $this->setRequestRows($request, $options);

        // get response as XML
        $xml = $gateway->getResponse($request, $this->getTransactionId());

        // load XML to object
        $xmlObject = simplexml_load_string($xml);

        return $this->handleReturnData($xmlObject);

    }

    /**
     * Searches Companies House API for company details given a company number
     * 
     * @param string $companyNumber string containing the search parameter
     * @param array $companyStatus array of company status's to search for
     * @return array
     */
    public function companyDetails($companyNumber)
    {
        $gateway = new CHXmlGateway();
        $gateway->setPassword($this->getPassword());
        $gateway->setUserId($this->getUserId());
        $request = $gateway->getCompanyDetails($companyNumber);

        // get response as XML
        $xml = $gateway->getResponse($request, $this->getTransactionID());

        // load XML to object
        $xmlObject = simplexml_load_string($xml);

        return $this->handleReturnData($xmlObject);

    }

    /**
     * Handles the retturned data by returning the error, or actual data
     * 
     * @param \SimpleXMLElement $xmlObject
     * @return array
     */
    private function handleReturnData(\SimpleXMLElement $xmlObject)
    {
         // check for errors
        $error = $this->detectError($xmlObject);

        if (strlen($error) > 0) {
            return $error;
        } else {
            // return results
            if (isset($xmlObject->Body)) {
                return $xmlObject->Body;
            } else {
                return array();
            }
        }

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
     * @return string
     */
    private function detectError(\SimpleXMLElement $xmlObject)
    {
        if (isset($xmlObject->GovTalkDetails->GovTalkErrors->Error)) {
            // we store only error number in the db, you can see description on ch website
            return $xmlObject->GovTalkDetails->GovTalkErrors;
        }

        return '';
    }

    /**
     * Sets number of request's rows
     *
     * @param Request $request
     * @param array $options
     * @return Request
     */
    private function setRequestRows($request, $options)
    {
        if (isset($options['searchRows']) &&
            is_numeric($options['searchRows']) &&
            $options['searchRows'] > 0 &&
            $options['searchRows'] < 50
           ) {
            $request->setSearchRows(intval($options['searchRows']));
        } else {
            $request->setSearchRows(self::DEFAULT_SEARCH_ROWS);
        }
        return $request;
    }

    /**
     * Gets transation ID
     *
     * @return int
     */
    public function getTransactionId()
    {
        return $this->transactionID;
    }

    /**
     * Gets transation ID
     *
     * @return int
     */
    public function setTransactionId($transactionID)
    {
        $this->transactionID = $transactionID;
    }

    /**
     * Gets password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Gets User ID
     *
     * @return string $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets User ID
     *
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
