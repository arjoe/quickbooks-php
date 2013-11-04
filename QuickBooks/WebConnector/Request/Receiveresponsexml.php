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
 * @author     Keith Palmer <keith@consolibyte.com>
 * @license    LICENSE.txt
 *
 * @package    QuickBooks
 * @subpackage Client
 */

/**
 * QuickBooks request base class
 */
QuickBooks_Loader::load('/QuickBooks/WebConnector/Request.php');

class QuickBooks_WebConnector_Request_ReceiveResponseXML extends QuickBooks_WebConnector_Request
{
    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
    public $ticket;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     * @todo       figure out what this field actually is
     */
    public $hresult;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
    public $message;

    /**
     * @deprecated Will become protected in next release.  Use accessor methods.
     */
    public $response;

    public function __construct($ticket = null, $response = null, $hresult = null, $message = null)
    {
        $this->ticket   = $ticket;
        $this->response = $response;
        $this->hresult  = $hresult;
        $this->message  = $message;
    }

    public function getHResult()
    {
        return $this->hresult;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getTicket()
    {
        return $this->ticket;
    }
}
