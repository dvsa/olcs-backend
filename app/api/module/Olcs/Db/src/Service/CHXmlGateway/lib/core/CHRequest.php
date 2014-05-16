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

/**
 * CHRequest 
 * 
 * @package chxmlgateway
 * @version $id$
 * @copyright 2009 Peter Reisinger
 * @author Peter Reisinger <p.reisinger@gmail.com> 
 * @license GNU General Public License
 */
interface CHRequest
{
    /**
     * getClass 
     *
     * class tag inside the envelope
     * 
     * @access public
     * @return string
     */
    public function getClass();

    /**
     * getData 
     *
     * array of all data submitted by user
     * 
     * @access public
     * @return array
     */
    public function getData();

    /**
     * getRequest 
     *
     * xml to be inserted inside Body tag in the envelope
     * 
     * @access public
     * @return xml
     */
    public function getRequest();
}
