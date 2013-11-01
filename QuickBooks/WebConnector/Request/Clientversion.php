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

class QuickBooks_WebConnector_Request_ClientVersion extends QuickBooks_WebConnector_Request
{
    /**
     * @var string
     * @deprecated To become protected in next release. Use accessor methods instead.
     */
    public $strVersion;

    public function __construct($version = null)
    {
        $this->strVersion = $version;
    }

    /**
     * Gets the client version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->strVersion;
    }
}

