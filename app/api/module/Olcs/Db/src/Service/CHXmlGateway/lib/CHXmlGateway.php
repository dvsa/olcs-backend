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
namespace Olcs\Db\Service\CHXmlGateway\lib;

use Olcs\Db\Service\ServiceAbstract;

/**
 * include parent interface used by all requests
 */
require_once('core/CHRequest.php');

/**
 * CHXmlGateway
 *
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com>
 * @license GNU General Public License
 */
class CHXmlGateway extends ServiceAbstract
{
    // --- start editing here --- //

    /**
     * password
     *
     * password from companies house
     *
     * @var string
     * @access private
     */
    private $password = 'XMLGatewayTestPassword';   // change

    /**
     * senderID
     *
     * sender id from companies house
     *
     * @var string
     * @access private
     */
    private $senderID = 'XMLGatewayTestUserID';   // change

    /**
     * emailAddress
     *
     * your email address
     * or set to null
     *
     * @var string
     * @access private
     */
    private $emailAddress = null; // set to your email or leave null

    // --- you can stop editing here --- //

    /**
     * getNameSearch
     *
     * @access public
     * @return NameSearch
     */
    public function getNameSearch($companyName, $dataSet)
    {
        require_once('core/NameSearch.php');
        return new NameSearch($companyName, $dataSet);
    }

    /**
     * getNumberSearch
     *
     * @param string $partialCompanyNumber
     * @param array $dataSet
     * @access public
     * @return void
     */
    public function getNumberSearch($partialCompanyNumber, array $dataSet)
    {
        require_once('core/NumberSearch.php');
        return new NumberSearch($partialCompanyNumber, $dataSet);
    }

    /**
     * getCompanyDetails
     *
     * @param strin $companyNumber
     * @access public
     * @return void
     */
    public function getCompanyDetails($companyNumber)
    {
        require_once('core/CompanyDetails.php');
        return new CompanyDetails($companyNumber);
    }

    public function getCompanyAppointments($companyNumber)
    {
        require_once('core/CompanyAppointments.php');
        return new CompanyAppointments($companyNumber);
    }

    public function getDocumentInfo($companyNumber)
    {
        require_once('core/DocumentInfo.php');
        return new DocumentInfo($companyNumber);
    }

    public function getDocument($companyNumber)
    {
        require_once('core/Document.php');
        return new Document($companyNumber);
    }

    /**
     * getMortgages
     *
     * @param string $companyNumber
     * @param string $companyName
     * @access public
     * @return void
     */
    public function getMortgages($companyNumber, $companyName)
    {
        require_once('core/Mortgages.php');
        return new Mortgages($companyNumber, $companyName);
    }

    /**
     * getOfficerSearch
     *
     * @param string $surname
     * @param string $officerType CUR | LLP | DIS | EUR
     * @access public
     * @return void
     */
    public function getOfficerSearch($surname, $officerType)
    {
        require_once('core/OfficerSearch.php');
        return new OfficerSearch($surname, $officerType);
    }

    /**
     * getResponse
     *
     * returns xml response from companies house
     *
     * @param CHRequest $request
     * @access public
     * @return xml
     */
    public function getResponse(CHRequest $request, $transactionID)
    {
        //echo $request->getRequest();
        // --- include Envelope class ---
        require_once('core/CHEnvelope.php');

        // --- create instance of envelope ---
        $envelope = new CHEnvelope($request, $transactionID, $this->senderID, $this->password, $this->emailAddress);

        // --- write into db ---
        //$this->insertInto($request->getClass(), $transID, $request->getData());
        // --- response xml from companies house ---

        $response = $envelope->getResponse();

        // --- check response for error and write into db ---
        //$this->setError($transactionID, $response);
        // --- return response ---
        return $response;
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
     * Sets sender id
     *
     * @param string $senderID
     */
    public function setUserId($senderID)
    {
        $this->senderID = $senderID;
    }
}
