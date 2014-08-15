<?php

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
 * Mortgages
 *
 * @uses CHRequest
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com>
 * @license GNU General Public License
 */
class Mortgages implements CHRequest
{

    /**
     * xml template file
     */
    const MORTGAGES_FILE = "/xml/mortgages.xml";

    /**
     * class
     *
     * class tag in the envelope
     *
     * @var string
     * @access private
     */
    private $class = 'Mortgages';

    /**
     * data
     *
     * holds values set by user - to be
     * sent in the request
     *
     * @var array
     * @access private
     */
    private $data = array();

    /**
     * __construct
     *
     * @param string $partialCompanyNumber
     * @param array $dataSet
     * @access public
     * @return void
     */
    public function __construct($companyNumber, $companyName)
    {
        $companyNumber = trim($companyNumber);

        $pattern = '/^[A-Z0-9]{8}$/';

        if (!preg_match($pattern, $companyNumber)) {
            throw new RestResponseException(
                'Company number has to be in this pattern: ' . $pattern, Response::STATUS_CODE_500
            );
        }
        $this->data['companyNumber'] = $companyNumber;

        // --- check if company name is allowed ---
        if (!strlen($companyName) >= 1 && strlen($companyName) <= 160) {
            throw new RestResponseException(
                'Searched company name must be in between 1-160 characters', Response::STATUS_CODE_500
            );
        }
        $this->data['companyName'] = $companyName;

        // TODO - I couldn't find out what the hell user reference is, but 0 works
        $this->data['userReference'] = 0;
    }

    /**
     * setSatisfiedChargesInd
     *
     * Indicates whether satisfied charges are required
     *
     * @param boolean $satisfiedChargesInd
     * @access public
     * @return void
     */
    public function setSatisfiedChargesInd($satisfiedChargesInd)
    {
        $this->data['satisfiedChargesInd'] = ($satisfiedChargesInd) ? 1 : 0;
    }

    /**
     * setStartDate
     *
     * @param string $date YYYY-MM-DD
     * @access public
     * @return void
     */
    public function setStartDate($date)
    {
        $date = trim($date);
        if (!preg_match('/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/', $date)) {
            throw new RestResponseException(
                'Date has to be in YYYY-MM-DD format', Response::STATUS_CODE_500
            );
        }
        $this->data['startDate'] = $date;
    }

    /**
     * setEndDate
     *
     * @param strin $date YYYY-MM-DD
     * @access public
     * @return void
     */
    public function setEndDate($date)
    {
        if (!preg_match('/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/', $date)) {
            throw new RestResponseException(
                'Date has to be in YYYY-MM-DD format', Response::STATUS_CODE_500
            );
        }
        $this->data['endDate'] = $date;
    }

    /**
     * setContinuationKey
     *
     * The continuation key allowing further data sets to be retrieved
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
        $body = simplexml_load_file(dirname(__FILE__) . self::MORTGAGES_FILE);

        // fill in compulsory fields
        $body->CompanyName = $this->data['companyName'];
        $body->CompanyNumber = $this->data['companyNumber'];
        $body->UserReference = $this->data['userReference'];

        // fill in optional fields
        if (isset($this->data['satisfiedChargesInd'])) {
            $body->addChild('SatisfiedChargesInd', $this->data['satisfiedChargesInd']);
        }

        if (isset($this->data['startDate'])) {
            $body->addChild('StartDate', $this->data['startDate']);
        }

        if (isset($this->data['endDate'])) {
            $body->addChild('EndDate', $this->data['endDate']);
        }

        // return xml
        return $body->asXML();
    }
}
