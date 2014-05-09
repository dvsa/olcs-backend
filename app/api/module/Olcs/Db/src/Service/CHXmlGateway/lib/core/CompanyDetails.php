<?php
namespace Olcs\Db\Service\CHXmlGateway\lib;

// for error codes
use OlcsCommon\Controller\AbstractRestfulController as AbstractRestfulController;

/*
+-------------------------------------------------------------------------------+
|   Copyright 2009 Peter Reisinger - p.reisinger@gmail.com                      |
|                                                                               |
|   This program is free software: you can redistribute it and/or modify        |
|   it under the terms of the GNU General Public License as published by        |
|   the Free Software Foundation, either version 3 of the License, or           |
|   (at your option) any later version.                                         |
|                                                                               |
|   This program is distributed in the hope that it will be useful,             |
|   but WITHOUT ANY WARRANTY; without even the implied warranty of              |
|   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
|   GNU General Public License for more details.                                |
|                                                                               |
|   You should have received a copy of the GNU General Public License           |
|   along with this program.  If not, see <http://www.gnu.org/licenses/>.       |
+-------------------------------------------------------------------------------+
 */

/**
 * CompanyDetails
 * 
 * @uses CHRequest
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com> 
 * @license GNU General Public License
 */
class CompanyDetails implements CHRequest
{
    /**
     * xml template file 
     */
    const COMPANY_DETAILS_FILE = "/xml/company-details.xml";

    /**
     * class 
     *
     * class tag in the envelope
     * 
     * @var string
     * @access private
     */
    private $class              = 'CompanyDetails';

    /**
     * data 
     *
     * holds values set by user - to be 
     * sent in the request
     * 
     * @var array
     * @access private
     */
    private $data               = array();

    /**
     * __construct 
     * 
     * @param string $partialCompanyNumber 
     * @param array $dataSet 
     * @access public
     * @return void
     */
    public function __construct($companyNumber)
    {
        $companyNumber = trim($companyNumber);

        $pattern = '/^[A-Z0-9]{8}$/';

        if (!preg_match($pattern, $companyNumber)) {
            throw new Exception(
                'Company number has to be in this pattern: '.$pattern,
                AbstractRestfulController::ERROR_INVALID_PARAMETER
            );
        }
        $this->data['companyNumber'] = $companyNumber;
    }

    /**
     * setMortTotals 
     *
     * Requests the return of mortgage totals
     * 
     * @param boolean $mortTotals 
     * @access public
     * @return void
     */
    public function setMortTotals($mortTotals)
    {
        $this->data['giveMortTotals'] = ($mortTotals) ? 1 : 0;
    }

    /**
     * getClass 
     * 
     * @access public
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * getData 
     *
     * contains all data set by user
     * 
     * @access public
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * getRequest 
     * 
     * @access public
     * @return xml
     */
    public function getRequest()
    {
        // load xml file
        $body = simplexml_load_file(dirname(__FILE__).self::COMPANY_DETAILS_FILE);

        // fill in compulsory fields
        $body->CompanyNumber = $this->data['companyNumber'];

        // fill in optional fields
        if (isset($this->data['giveMortTotals'])) {
            $body->addChild('GiveMortToals', $this->data['giveMortTotals']);
        }

        // return xml
        return $body->asXML();
    }
}
