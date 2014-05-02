<?php

/**
 * Companies House REST controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Olcs\Db\Controller;

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * Companies House REST controller
 *
 */
class CompaniesHouseController extends AbstractBasicRestServerController
{
    protected $allowedMethods = array(
        'getList',
        'get'
    );

    /**
     * Searches Companies House for a given partial company name
     *  
     * @return Response
     */
    public function nameSearchAction()
    {
        $companyName = $this->params('companyname');
     
        
        try {
            
            // get new transaction ID
            $companiesHouseRequest = $this->getService('CompaniesHouseRequest')->initiateRequest('nameSearch');
            $transactionId = $companiesHouseRequest->getId();
            
            $companiesHouse = $this->getService('CompaniesHouse');
            
            // pass transaction ID to Companies House Service
            $companiesHouse->setTransactionID($transactionId);
            
            // make request
            $data = $companiesHouse->nameSearch($companyName, 'LIVE', array('searchRows' => 10));
            
            if (isset($data->Error)) {
                throw new RestResponseException('Companies house API error', Response::STATUS_CODE_500);
            }
            
            //@todo: investigate response and correct
            $response = array(
               'Type' => 'results',
               'Count' => 1,
               'Results' => $data
            );

        } catch(\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }
        
        return $this->respond(Response::STATUS_CODE_200, 'Information found', $response);
    }
        
    /**
     * Searches Companies House for a given partial company number
     *  
     * @return Response
     */
    public function numberSearchAction()
    {
        $companyNumber = $this->params('companynumber');
     
        
        try {
            
            // get new transaction ID
            $companiesHouseRequest = $this->getService('CompaniesHouseRequest')->initiateRequest('numberSearch');
            $transactionId = $companiesHouseRequest->getId();
            
            $companiesHouse = $this->getService('CompaniesHouse');
            
            // pass transaction ID to Companies House Service
            $companiesHouse->setTransactionID($transactionId);
            
            // make request
            $data = $companiesHouse->numberSearch($companyNumber, array('LIVE'), array('searchRows' => 10));
            
            if (isset($data->Error)) {
                throw new RestResponseException('Companies house API error', Response::STATUS_CODE_500);
            }
            
            //@todo: investigate response and correct
            $response = array(
               'Type' => 'results',
               'Count' => 1,
               'Results' => $data
            );

        } catch(\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }
        
        return $this->respond(Response::STATUS_CODE_200, 'Information found', $response);
    }    
    
    /**
     * Retrieves the company details for a given company number
     *  
     * @return Response
     */
    public function companyDetailsAction()
    {
        $companyNumber = $this->params('companynumber');
     
        
        try {
            
            // get new transaction ID
            $companiesHouseRequest = $this->getService('CompaniesHouseRequest')->initiateRequest('numberSearch');
            $transactionId = $companiesHouseRequest->getId();
            
            $companiesHouse = $this->getService('CompaniesHouse');
            
            // pass transaction ID to Companies House Service
            $companiesHouse->setTransactionID($transactionId);
            
            // make request
            $data = $companiesHouse->companyDetails($companyNumber);
            
            if (isset($data->Error)) {
                throw new RestResponseException('Companies house API error', Response::STATUS_CODE_500);
            }
            
            //@todo: investigate response and correct
            $response = array(
               'Type' => 'results',
               'Count' => 1,
               'Results' => $data
            );

        } catch(\Exception $ex) {

            throw new RestResponseException($ex->getMessage(), Response::STATUS_CODE_500);
        }
        
        return $this->respond(Response::STATUS_CODE_200, 'Information found', $response);
    }    
    
}
