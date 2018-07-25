<?php

/**
 * Companies House Request Service
 *
 * @note migrated from Olcs\Db\Service
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service;

use Dvsa\Olcs\Api\Domain\Exception\RestResponseException;
use Interop\Container\ContainerInterface;
use Olcs\Logging\Log\Logger;
use Zend\Http\Response;
use CompaniesHouse\CHXmlGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Companies House Request Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class CompaniesHouseService implements FactoryInterface
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
     * Proxy for accessing Companies House API
     */
    private $proxy = false;

    /**
     * User ID for the Companies House API
     */
    private $userId;

    private $config;

    /**
     * Allowed appointment types
     * @see http://xmlgw.companieshouse.gov.uk/data_usage_guide_apr_2014.pdf
     */
    private $allowedAppointmentTypes = ['DIR', 'LLPMEM', 'LLPGPART', 'LLPPART', 'RECMAN', 'FACTOR', 'LLPDMEM'];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->config = $container->get('Config');
        return $this;
    }

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
        $value = $data['value'];

        $this->setCredentials();

        $transactionId = $this->generateTransactionId();

        $this->setTransactionId($transactionId);

        Logger::info(
            'Companies House request',
            [
                'data' => compact('requestType', 'method', 'value', 'transactionId')
            ]
        );

        try {
            $result = $this->$method($this->getNewGateway(), $value);
            Logger::info('Companies House response', ['data' => compact('result')]);
        } catch (\Exception $ex) {
            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }

        $finalResult = $this->getFinalResult($result, $method);

        return array('Count' => count($finalResult), 'Results' => $finalResult);
    }

    /**
     * Get a new instance of gateway
     *
     * @return \CompaniesHouse\CHXmlGateway
     */
    public function getNewGateway()
    {
        $gateway = new CHXmlGateway();
        $gateway->setPassword($this->getPassword());
        $gateway->setUserId($this->getUserId());
        if ( $this->getProxy() != false ) {
            $gateway->setProxyUrl($this->getProxy());
        }

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
     * Searches Companies House API for company appointments and gets Company Officers with
     * specific appointment types and Current status
     *
     * @param object $gateway
     * @param string $companyNumber string containing the search parameter
     * @return array
     */
    protected function currentCompanyOfficers($gateway, $companyNumber)
    {
        $request = $gateway->getCompanyAppointments($companyNumber);
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
        $this->setPassword($this->config['companies_house_credentials']['password']);
        $this->setUserId($this->config['companies_house_credentials']['userId']);

        if ( isset($this->config['companies_house_connection']['proxy']) ) {
            $this->setProxy($this->config['companies_house_connection']['proxy']);
        }
    }

    /**
     * Get final result from result
     *
     * @param object $result
     * @param string $method
     * @return array
     */
    private function getFinalResult($result, $method)
    {
        $finalResult = array();

        if (array_key_exists('NameSearch', $result) !== false) {

            foreach ($result->NameSearch->CoSearchItem as $item) {
                $finalResult[] = $item;
            }

        } elseif (array_key_exists('NumberSearch', $result) !== false) {

            foreach ($result->NumberSearch->CoSearchItem as $item) {
                $finalResult[] = $item;
            }

        } elseif (array_key_exists('CompanyDetails', $result) !== false) {

            foreach ($result->CompanyDetails as $item) {
                $finalResult[] = $item;
            }
        } elseif (array_key_exists('CompanyAppointments', $result) !== false && $method == 'currentCompanyOfficers') {
            $result = $result->CompanyAppointments;
            if (array_key_exists('CoAppt', $result) !== false) {
                foreach ($result->CoAppt as $item) {
                    $appointmentType = (string)$item->AppointmentType;
                    $appointmentStatus = (string)$item->AppointmentStatus;
                    if ($appointmentStatus == 'CURRENT' &&
                        in_array($appointmentType, $this->allowedAppointmentTypes) !== false) {
                        $finalResult[] = [
                            'title'       => ucfirst(strtolower((string)$item->Person->Title)),
                            'forename'   => (string)$item->Person->Forename,
                            'familyName'     => (string)$item->Person->Surname,
                            'birthDate' => (string)$item->Person->DOB,
                        ];
                    }
                }
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
        if (!in_array(
            $data['type'],
            array('nameSearch', 'numberSearch', 'companyDetails', 'currentCompanyOfficers')
        )) {
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

        if (isset($return->Error) && $return->Error->Number != 600 && substr($return->Error->Number, 0, 1) !== '9') {
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
     * Gets proxy
     *
     * @return string
     */
    private function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Sets proxy
     *
     * @param string $proxy
     */
    private function setProxy($proxy)
    {
        $this->proxy = $proxy;
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

    /**
     * @return int
     */
    protected function generateTransactionId()
    {
        return microtime(true)*10000;
    }
}
