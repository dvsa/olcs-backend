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

use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;

/**
 * interface is included in CHXmlGateway
 */

/**
 * NameSearch 
 * 
 * @uses CHRequest
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com> 
 * @license GNU General Public License
 */
class NameSearch implements CHRequest
{
    /**
     * xml file 
     */
    const NAME_SEARCH_FILE  = "/xml/name-search.xml";

    /**
     * class 
     *
     * class tag in the envelope
     * 
     * @var string
     * @access private
     */
    private $class          = 'NameSearch';

    /**
     * data 
     *
     * data to be submitted
     * like company name, data set etc.
     * 
     * @var array
     * @access private
     */
    private $data           = array();

    /**
     * __construct 
     * 
     * @param string $companyName between 1 and 160 characters
     * @param string $dataSet LIVE, DISSOLVED, FORMER, PROPOSED
     * @access public
     * @return void
     */
    public function __construct($companyName, $dataSet)
    {
        $dataSet = strtoupper($dataSet);

        // --- check if data set is allowed ---
        if ($dataSet != 'LIVE' && $dataSet != 'DISSOLVED' &&
            $dataSet != 'FORMER' && $dataSet != 'PROPOSED') {
            throw new RestResponseException(
                'Data Set can be one of the following: LIVE, DISSOLVED, FORMER, PROPOSED', Response::STATUS_CODE_500
            );
        }
        $this->data['dataSet'] = $dataSet;

        // --- check if company name is allowed ---
        if (! strlen($companyName) >= 1 && strlen($companyName) <= 160) {
            throw new RestResponseException(
                'Searched company name must be in between 1-160 characters', Response::STATUS_CODE_500
            );
        }
        $this->data['companyName'] = $companyName;
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
        // --- check if search rows is integer ---
        if (!preg_match('/^\d+$/', $searchRows)) {
            throw new RestResponseException('Search rows has to be integer', Response::STATUS_CODE_500);
        }
        $this->data['searchRows'] = $searchRows;
    }

    /**
     * setContinuationKey 
     * 
     * @param string $continuationKey 
     * @access public
     * @return void
     */
    public function setContinuationKey($continuationKey)
    {
        $this->data['continuationKey'] = $continuationKey;
    }

    /**
     * setRegressionKey 
     * 
     * @param string $regressionKey 
     * @access public
     * @return void
     */
    public function setRegressionKey($regressionKey)
    {
        $this->data['regressionKey'] = $regressionKey;
    }

    /**
     * getClass 
     *
     * return class - this is used in the envelope
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
        $body = simplexml_load_file(dirname(__FILE__).self::NAME_SEARCH_FILE);

        // fill in compulsory fields
        $body->CompanyName = $this->data['companyName'];
        $body->DataSet     = $this->data['dataSet'];

        // fill in optional fields
        if (isset($this->data['searchRows'])) {
            $body->addChild('SearchRows', $this->data['searchRows']);
        }

        if (isset($this->data['continuationKey'])) {
            $body->addChild('ContinuationKey', $this->data['continuationKey']);
        }

        if (isset($this->data['regressionKey'])) {
            $body->addChild('RegressionKey', $this->data['regressionKey']);
        }

        // return xml
        return $body->asXML();
    }
}
