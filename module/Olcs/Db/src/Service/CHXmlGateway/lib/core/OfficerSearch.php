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

/**
 * interface is included in CHXmlGateway
 */
// for error codes
use OlcsCommon\Controller\AbstractRestfulController as AbstractRestfulController; 

/**
 * OfficerSearch
 * 
 * @uses CHRequest
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com> 
 * @license GNU General Public License
 */
class OfficerSearch implements CHRequest
{
    /**
     * xml file 
     */
    const OFFICER_SEARCH_FILE  = "/xml/officer-search.xml";

    /**
     * class 
     *
     * class tag in the envelope
     * 
     * @var string
     * @access private
     */
    private $class          = 'OfficerSearch';

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
     * @param string $surname 
     * @param string $officerType 
     * @access public
     * @return void
     */
    public function __construct($surname, $officerType)
    {
        $this->data['surname'] = $surname;

        if ($officerType != 'CUR' && $officerType != 'LLP' && 
            $officerType != 'DIS' && $officerType != 'EUR') 
        {
            throw new Exception('Officer type can be on of: CUR, LLP, DIS, EUR', AbstractRestfulController::ERROR_INVALID_PARAMETER);
        }
        $this->data['officerType'] = $officerType;
    }

    public function setForename($forename)
    {
        $this->data['forename'] = $forename;
    }

    public function setSecondForename($secondForename)
    {
        $this->data['secondForename'] = $secondForename;
    }

    public function setPostTown($postTown)
    {
        $this->data['postTown'] = $postTown;
    }

    public function setIncludeResignedInd($includeResignedInd)
    {
        $this->data['includeResignedInd'] = $includeResignedInd;
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
        $body = simplexml_load_file(dirname(__FILE__).self::OFFICER_SEARCH_FILE);

        // fill in compulsory fields
        $body->Surname = $this->data['surname'];

        // fill in optional fields
        if (isset($this->data['forename'])) {
            $body->addChild('Forename', $this->data['forename']);
        }

        if (isset($this->data['secondForename'])) {
            $body->addChild('Forename', $this->data['secondForename']);
        }

        if (isset($this->data['postTown'])) {
            $body->addChild('PostTown', $this->data['postTown']);
        }

        $body->addChild('OfficerType', $this->data['officerType']);

        if (isset($this->data['includeResignedInd'])) {
            $body->addChild('IncludeResignedInd', $this->data['includeResignedInd']);
        }

        if (isset($this->data['continuationKey'])) {
            $body->addChild('ContinuationKey', $this->data['continuationKey']);
        }

        // return xml
        return $body->asXML();
    }
}

