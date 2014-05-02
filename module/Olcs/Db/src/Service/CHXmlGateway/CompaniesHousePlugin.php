<?php

/**
 * Plugin to talk to Companies House
 *
 * @author Shaun Lizzio
 */

namespace OlcsCommon\Controller\Plugin\CHXmlGateway;
// for error codes
use OlcsCommon\Controller\AbstractRestfulController as AbstractRestfulController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Json\Json as Json;

require_once('lib/CHXmlGateway.php');

class CompaniesHousePlugin  extends \CHXmlGateway
{
    
    const DEFAULT_SEARCH_ROWS = 10;
    
    private $transactionID;
    
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
        $request = $this->getNameSearch($companyName, $companyStatus);

        // set the number of rows required ('searchRows' in $options), default to 10
        $request = $this->setRequestRows($request, $options);
        
        // get response as XML
        $xml = $this->getResponse($request, $this->getTransactionID());

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
    public function numberSearch($partialCompanyNumber, $companyStatus = array('LIVE', 'FORMER', 'DISOLVED', 'PROPOSED'), $options = array())
    {
                
        $request = $this->getNumberSearch($partialCompanyNumber, $companyStatus);

        // set the number of rows required ('searchRows' in $options), default to 10
        $request = $this->setRequestRows($request, $options);
        
        // get response as XML
        $xml = $this->getResponse($request, $this->getTransactionID());

        // load XML to object
        $xmlObject = simplexml_load_string($xml);

        return $this->handleReturnData($xmlObject);
        
    }
    
    /**
     * Searches Companies House API for company details given a company number
     * 
     * @param string $companyNumber string containing the search parameter
     * @param array $companyStatus array of company status's to search for
     * @param array $options array of options, at present only searchRows is processed
     * @return array
     */
    public function companyDetails($companyNumber, $options = array())
    {
                
        $request = $this->getCompanyDetails($companyNumber);
        
        // get response as XML
        $xml = $this->getResponse($request, $this->getTransactionID());

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
        
        if (strlen($error) > 0)
        {
            return $error;
        }
        else 
        {
            // return results 
            if (isset($xmlObject->Body))
            {
                return $xmlObject->Body;
            }
            else
            {
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
    
    private function setRequestRows($request, $options)
    {
        if (isset($options['searchRows']) && 
            is_numeric($options['searchRows']) &&
            $options['searchRows'] > 0 && 
            $options['searchRows'] < 50
           )
        {
            $request->setSearchRows(intval($options['searchRows']));
        }
        else 
        {
            $request->setSearchRows(self::DEFAULT_SEARCH_ROWS);
        }
        
        return $request;
    }
    
    public function getTransactionID()
    {
        return $this->transactionID;
    }
    
    public function setTransactionID($transactionID)
    {
        $this->transactionID = $transactionID;
    }
}

