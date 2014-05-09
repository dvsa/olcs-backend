<?php
namespace Olcs\Db\Service\CHXmlGateway\lib;

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

// for error codes
use OlcsCommon\Controller\AbstractRestfulController as AbstractRestfulController;

/**
 * NumberSearch 
 * 
 * @uses CHRequest
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com> 
 * @license GNU General Public License
 */
class NumberSearch implements CHRequest
{
    /**
     * xml template file 
     */
    const NUMBER_SEARCH_FILE    = "/xml/number-search.xml";

    /**
     * class 
     *
     * class tag in the envelope
     * 
     * @var string
     * @access private
     */
    private $class              = 'NumberSearch';

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
    public function __construct($partialCompanyNumber, array $dataSet)
    {
        $partialCompanyNumber = trim($partialCompanyNumber);

        $pattern = '/^([A-Z0-9\*]{1,8}[*]{0,1}){1,8}$/';

        if (!preg_match($pattern, $partialCompanyNumber)) {
            throw new Exception(
                'Company number has to be in this pattern: ' . $pattern,
                AbstractRestfulController::ERROR_INVALID_PARAMETER
            );
        }
        $this->data['partCnumber'] = $partialCompanyNumber;

        // allowed values in the data set
        $allowedDataSet = array('LIVE' => true, 'FORMER' => true, 'DISSOLVED' => true, 'PROPOSED' => true);

        $cleanDataSet = array();
        // go trough user's array
        foreach ($dataSet as $value) {
            $value = strtoupper($value);
            // check if value is allowed
            if (!$allowedDataSet[$value]) {
                throw new Exception(
                    $value.' is not allowed as data set value',
                    AbstractRestfulController::ERROR_INVALID_PARAMETER
                );
            }
            $cleanDataSet[] = $value;
        }

        // check if at least one data set is set
        if (empty($cleanDataSet)) {
            throw new Exception('You need to set data set', AbstractRestfulController::ERROR_MISSING_FIELDS);
        }

        $this->data['dataSet']  = $cleanDataSet;
    }

    /**
     * setSearchRows 
     * 
     * @param int $searchRows 
     * @access public
     * @return void
     */
    public function setSearchRows($searchRows)
    {
        if (!preg_match('/^\d+$/', $searchRows)) {
            throw new Exception('value has to be integer', AbstractRestfulController::ERROR_INVALID_PARAMETER);
        }
        $this->data['searchRows'] = $searchRows;
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
        $data = $this->data;
        // array has to be one dimension
        $data['dataSet'] = implode(',', $data['dataSet']);
        return $data;
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
        $body = simplexml_load_file(dirname(__FILE__).self::NUMBER_SEARCH_FILE);

        // fill in compulsory fields
        $body->PartialCompanyNumber = $this->data['partCnumber'];

        foreach ($this->data['dataSet'] as $value) {
            $body->addChild('DataSet', $value);
        }

        // fill in optional fields
        if (isset($this->data['searchRows'])) {
            $body->addChild('SearchRows', $this->data['searchRows']);
        }

        // return xml
        return $body->asXML();
    }
}
