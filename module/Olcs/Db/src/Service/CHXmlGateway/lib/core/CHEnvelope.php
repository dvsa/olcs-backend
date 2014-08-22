<?php

namespace Olcs\Db\Service\CHXmlGateway\lib;

use \Zend\Http\Client as HttpClient;
use \Zend\Http\Request as Request;
use \Zend\Uri\Http as HttpUri;
use \Zend\Http\Header\Accept;
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
 * CHEnvelope
 *
 * Class modified to use Zend HTTP Request methods and enable proxies by Jess
 * as a temporary measure - see OLCS-2947
 *
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 * @author Peter Reisinger <p.reisinger@gmail.com>
 * @license GNU General Public License
 */
class CHEnvelope
{
    /* template xml file for envelope */

    const ENVELOPE_FILE = '/xml/envelope.xml';

    /**
     * domain
     *
     * domain for sending xml requests (envelopes)
     *
     * @var string
     * @access private
     */
    private $domain = 'xmlgw.companieshouse.gov.uk';

    /**
     * path
     *
     * path for sending xml requests (envelopes)
     *
     * @var string
     * @access private
     */
    private $path = '/v1-0/xmlgw/Gateway';

    /**
     * envelopeVersion
     *
     * @var string
     * @access private
     */
    private $envelopeVersion = '1.0';

    /**
     * qualifier
     *
     * @var string
     * @access private
     */
    private $qualifier = 'request';

    /**
     * method
     *
     * @var string
     * @access private
     */
    private $method = 'CHMD5';

    /**
     * transactionID
     *
     * unique for every transaction,
     * has to be autoincrement
     *
     * @var integer
     * @access private
     */
    private $transactionID;

    /**
     * senderID
     *
     * obtained from companies house
     *
     * @var string
     * @access private
     */
    private $senderID;

    /**
     * value
     *
     * md5 hash of sender id, password and transaction id
     *
     * @var string
     * @access private
     */
    private $value;

    /**
     * emailAddress
     *
     * @var string
     * @access private
     */
    private $emailAddress;

    /**
     * request
     *
     * request inside envelope
     *
     * @var Request
     * @access private
     */
    private $request;

    /**
     * HTTP client
     */
    private $client;

    /**
     * __construct
     *
     * @param CHRequest $request
     * @param string $transactionID
     * @param string $senderID
     * @param string $password
     * @param string $emailAddress
     * @param string $proxyUrl
     * @access public
     * @return void
     */
    public function __construct(CHRequest $request, $transactionID, $senderID, $password, $emailAddress = null, $proxyUrl = null)
    {
        $this->request = $request;     // Request object
        $this->transactionID = $transactionID;
        $this->senderID = $senderID;
        $this->value = md5($senderID . $password . $transactionID);
        $this->emailAddress = $emailAddress;
        $this->proxyUrl = $proxyUrl;
    }

    /**
     * getResponse
     *
     * returns response from companies house
     *
     * @access public
     * @return xml
     */
    public function getResponse()
    {
        // --- prepare xml request for submission ---
        $xml = simplexml_load_file(dirname(__FILE__) . self::ENVELOPE_FILE);

        $xml->EnvelopeVersion = $this->envelopeVersion;
        $xml->Header->MessageDetails->Class = $this->request->getClass();
        $xml->Header->MessageDetails->Qualifier = $this->qualifier;
        $xml->Header->MessageDetails->TransactionID = $this->transactionID;
        $xml->Header->SenderDetails->IDAuthentication->SenderID = $this->senderID;
        $xml->Header->SenderDetails->IDAuthentication->Authentication->Method = $this->method;
        $xml->Header->SenderDetails->IDAuthentication->Authentication->Value = $this->value;
        if (!is_null($this->emailAddress)) {
            $xml->Header->SenderDetails->EmailAddress = $this->emailAddress;
        }

        // --- convert from simplexml to xml ---
        $xml = $xml->asXml();

        // --- replace 'Body' placeholder with body
        // reomove xml declaration from body, so it can be inserted
        $body = trim(preg_replace('/<\?xml.*\?>/', '', $this->request->getRequest(), 1));

        // insert body in the xml request - envelope
        $xml = trim(str_replace('<?Body ?>', $body, $xml));

        //echo $xml;exit;

        /*
          +-------------------+
          |   Send request    |
          +-------------------+
         */

        $resHeader = '';
        $response = '';
        $this->request=new Request();
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setUri($this->path);
        $this->request->getHeaders()->addHeaders(array(
            'Content-Type' => 'text/xml'
        ));
        $this->request->setContent($xml);

        $this->client = new HttpClient();
        if ( $this->proxyUrl != null ) {
            list($proxyHost,$proxyPort)=explode(":",$this->proxyUrl);
            $config = array(
                'adapter'    => 'Zend\Http\Client\Adapter\Proxy',
                'proxy_host' => $proxyHost,
                'proxy_port' => $proxyPort
            );
            $this->client->setOptions($config);
        }
        $this->client->setRequest($this->request);
        $this->client->setUri('http://'.$this->domain.$this->path);
        $response=$this->client->send($this->request);

        if ( $response->getStatusCode() == 403 ) {
            return false;
        }

        $responseBody=$response->getBody();
        return $responseBody;
    }
}
