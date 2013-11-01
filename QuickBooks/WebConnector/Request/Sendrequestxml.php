<?php

/**
 * 
 * 
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 * 
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt 
 * 
 * @package QuickBooks
 * @subpackage Client
 */

/**
 * QuickBooks request base class
 */
QuickBooks_Loader::load('/QuickBooks/WebConnector/Request.php');

class QuickBooks_WebConnector_Request_SendRequestXML extends QuickBooks_WebConnector_Request
{
    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
    public $ticket;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
	public $strHCPResponse;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
	public $strCompanyFileName;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
	public $qbXMLCountry;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
	public $qbXMLMajorVers;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
	public $qbXMLMinorVers;
	
	public function __construct($ticket = null, $hcpresponse = null, $companyfile = null, $country = null, $majorversion = null, $minorversion = null)
	{
		$this->ticket = $ticket;
		$this->strHCPResponse = $hcpresponse;
		$this->strCompanyFileName = $companyfile;
		$this->qbXMLCountry = $country;
		$this->qbXMLMajorVers = (int) $majorversion;
		$this->qbXMLMinorVers = (int) $minorversion;
	}

    public function getCompanyFilename() {
        return $this->strCompanyFileName;
    }

    public function getHcpResponse() {
        return $this->strHCPResponse;
    }

    public function getQbXmlCountry() {
        return $this->qbXMLCountry;
    }

    public function getQbXmlMajorVersion() {
        return $this->qbXMLMajorVers;
    }

    public function getQbXmlMinorVersion() {
        return $this->qbXMLMinorVers;
    }

    public function getTicket() {
        return $this->ticket;
    }
}
