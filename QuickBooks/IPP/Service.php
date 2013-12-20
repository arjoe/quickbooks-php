<?php

/**
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 *
 * @package    QuickBooks
 * @subpackage IPP
 */

abstract class QuickBooks_IPP_Service
{

    /**
     * @var \QuickBooks_IPP_Context
     */
    protected $_context;

    /**
     * The last raw XML request
     * @var string
     */
    protected $_last_request;

    /**
     * The last raw XML response
     * @var string
     */
    protected $_last_response;

    /**
     *
     * @var string
     */
    protected $_last_debug;

    protected $_flavor;

    /**
     * The last error code
     * @var string
     */
    protected $_errcode;

    /**
     * The last error message
     * @var string
     */
    protected $_errtext;

    /**
     * The last error detail
     * @var string
     */
    protected $_errdetail;

    /**
     * Initializes a new instance of the {@link QuickBooks_IPP_Service} class.
     *
     * @param \QuickBooks_IPP_Context $context
     */
    public function __construct(\QuickBooks_IPP_Context $context = null)
    {
        $this->_errcode = QuickBooks_IPP::ERROR_OK;

        $this->_last_request  = null;
        $this->_last_response = null;
        $this->_last_debug    = array();

        $this->_flavor  = null; // auto-detect
        $this->_context = $context;
    }

    public function useIDSParser(\QuickBooks_IPP_Context $Context, $true_or_false)
    {
        $IPP = $Context->IPP();

        return $IPP->useIDSParser((boolean)$true_or_false);
    }

    protected function _cdc($Context, $realmID, $entities, $timestamp, $page, $size)
    {
        $IPP = $Context->IPP();

        // Send the data to IPP 
        //                  $Context, $realm, $resource, $optype, $xml = '', $ID = null
        $return = $IPP->IDS($Context, $realmID, null, QuickBooks_IPP_IDS::OPTYPE_CDC, array(
            $entities,
            $timestamp
        ));

        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK)
        {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    protected function _syncStatus(\QuickBooks_IPP_Context $Context, $realmID, $resource, $IDType)
    {
        $IPP = $Context->IPP();

        if ($IPP->version() == QuickBooks_IPP_IDS::VERSION_2) {
            return $this->_syncStatus_v2($Context, $realmID, $resource, $IDType);
        } else {
            return false;
        }
    }

    protected function _syncStatus_v2(\QuickBooks_IPP_Context $Context, $realmID, $resource, $IDType)
    {
        $IPP = $Context->IPP();

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<SyncStatusRequest xmlns="http://www.intuit.com/sb/cdm/v2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.intuit.com/sb/cdm/xmlrequest RestDataFilter.xsd">
    <OfferingId>ipp</OfferingId>
    <SyncStatusParam>
        <IdSet>';

        $parse = QuickBooks_IPP_IDS::parseIDType($IDType);
        $xml .= '<Id idDomain="' . $parse['domain'] . '">' . $parse['ID'] . '</Id>' . QUICKBOOKS_CRLF;

        $xml .= '
        <IdSet>
        <ObjectType>' . $resource . '</ObjectType>
    </SyncStatusParam>
</SyncStatusRequest>';

        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_SYNCSTATUS, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        return $return;
    }

    protected function _report(\QuickBooks_IPP_Context $Context, $realmID, $resource, $xml = '')
    {
        $IPP = $Context->IPP();

        if (!$xml) {
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
            $xml .= '<' . $resource . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '"></' . $resource . '>';
        }

        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_REPORT, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        return $return;
    }

    protected function _delete(\QuickBooks_IPP_Context $Context, $realmID, $resource, $IDType, $xml = '')
    {
        $IPP = $Context->IPP();

        if (!$xml) {
            $parse = QuickBooks_IPP_IDS::parseIDType($IDType);

            if ($IPP->getVersion() == QuickBooks_IPP_IDS::VERSION_2) {
                $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
                $xml .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->getVersion() . '">' . QUICKBOOKS_CRLF;
                $xml .= '	<TransactionIdSet>' . QUICKBOOKS_CRLF;
                $xml .= '		<Id idDomain="' . $parse[0] . '">' . $parse[1] . '</Id>' . QUICKBOOKS_CRLF;
                $xml .= '	</TransactionIdSet>' . QUICKBOOKS_CRLF;
                $xml .= '</' . $resource . 'Query>';
            } else {
                $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
                $xml .= '<' . $resource . ' xmlns="http://schema.intuit.com/finance/v3" domain="' . $IPP->getFlavor() . '" sparse="true">' . QUICKBOOKS_CRLF;
                $xml .= '   <Id idDomain="' . $parse[0] . '">' . $parse[1] . '</Id>' . QUICKBOOKS_CRLF;
                $xml .= '</' . $resource . '>';
            }
        }

        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_DELETE, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if (count($return)) {
            return $return[0];
        }

        return null;
    }

    protected function _guessResource($xml, $optype)
    {
        $tmp = explode('_', get_class($this));

        return end($tmp);
    }

    public function rawQuery(\QuickBooks_IPP_Context $Context, $realmID, $xml, $resource = null)
    {
        if (!$resource) {
            $resource = $this->_guessResource($xml, QuickBooks_IPP_IDS::OPTYPE_QUERY);
        }

        return $this->_findAll($Context, $realmID, $resource, null, null, null, null, $xml);
    }

    public function rawAdd(\QuickBooks_IPP_Context $Context, $realmID, $xml, $resource = null)
    {
        $IPP = $Context->IPP();

        if (!$resource) {
            $resource = $this->_guessResource($xml, QuickBooks_IPP_IDS::OPTYPE_ADD);
        }

        // Send the data to IPP
        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_ADD, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK) {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    protected function _map($list, $key, $value)
    {
        $map = array();
        foreach ($list as $Object) {
            $map[$Object->get($key)] = $Object->get($value);
        }

        return $map;
    }

    protected function _find()
    {
    }

    /**
     *
     * Returns false on error, and sets $IPP->errorCode, $IPP->errorText, and $IPP->errorDetail
     *
     * Added $options array in 09/2012:
     *   Supported array keys for QuickBooks Desktop are:
     *     ActiveOnly       => true/false (False by default. May not be used with DeletedObjects)
     *     DeletedObjects   => true/false (False by default. May not be used with ActiveOnly)
     *   Supported array keys for QuickBooks Online are:
     *     (none yet)
     */
    protected function _findAll(\QuickBooks_IPP_Context $Context, $realmID, $resource, $query = null, $sort = null, $page = 1, $size = 50, $xml = '', $options = array())
    {
        $IPP    = $Context->IPP();
        $flavor = $IPP->flavor();

        if ($flavor == QuickBooks_IPP_IDS::FLAVOR_DESKTOP) {
            if (!$xml) {
                $options_string = '';

                $xml = '';
                $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
                $xml .= '<' . $resource . 'Query ' . $options_string . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . QUICKBOOKS_CRLF;

                if ($size) {
                    $xml .= '	<StartPage>' . (int)$page . '</StartPage>' . QUICKBOOKS_CRLF;
                    $xml .= '	<ChunkSize>' . (int)$size . '</ChunkSize>' . QUICKBOOKS_CRLF;
                }

                $xml .= $query;

                $xml .= '</' . $resource . 'Query>';
            }
        } else {
            if ($flavor == QuickBooks_IPP_IDS::FLAVOR_ONLINE) {
                switch ($IPP->getVersion()) {
                    case QuickBooks_IPP_IDS::VERSION_2:
                        if (!$xml) {
                            if (is_array($query) and count($query) > 0) {
                                $xml = http_build_query(array_merge(array(
                                    'PageNum'        => (int)$page,
                                    'ResultsPerPage' => (int)$size,
                                ), (array)$query));
                            } else {
                                $xml = http_build_query(array_merge(array(
                                    'PageNum'        => (int)$page,
                                    'ResultsPerPage' => (int)$size,
                                )));

                                $xml .= $query;
                            }
                        }
                        break;

                    case QuickBooks_IPP_IDS::VERSION_3:
                        $xml = "SELECT * FROM $resource STARTPOSITION $page MAXRESULTS $size";
                        break;
                }
            }
        }

        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_QUERY, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK) {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    /**
     * Get an IDS object by Name (i.e. get a customer by the QuickBooks Name field)
     *
     * @param QuickBooks_IPP_Context $Context
     * @param integer                $realmID
     * @param string                 $resource
     * @param string                 $name
     * @param string                 $xml
     *
     * @return QuickBooks_IPP_Object
     */
    protected function _findByName(\QuickBooks_IPP_Context $Context, $realmID, $resource, $name, $xml = '')
    {
        $IPP = $Context->IPP();

        if ($IPP->flavor() == QuickBooks_IPP_IDS::FLAVOR_DESKTOP) {
            if (!$xml) {
                $xml = '';
                $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
                $xml .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . QUICKBOOKS_CRLF;
                $xml .= '	<FirstLastInside>' . QuickBooks_XML::encode($name) . '</FirstLastInside>' . QUICKBOOKS_CRLF;
                $xml .= '</' . $resource . 'Query>';
            }
        } else {
            if ($IPP->getVersion() == QuickBooks_IPP_IDS::VERSION_3) {
                $name = str_replace("'", "\\'", $name);
                $xml  = "SELECT * FROM $resource WHERE DisplayName = '$name'";
            } else {
                $xml = http_build_query(array('Filter' => 'Name :EQUALS: ' . $name));
            }
        }

        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_QUERY, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if (is_array($return) && count($return) > 0) {
            return $return[0];
        }

        return false;
    }

    /**
     * Add an IDS object via IPP
     *
     * @param QuickBooks_IPP_Context $Context
     * @param integer                $realmID
     * @param string                 $resource
     * @param object                 $Object
     *
     * @return integer
     */
    protected function _add(\QuickBooks_IPP_Context $Context, $realmID, $resource, $Object)
    {
        if ($Context == null) {
            $Context = $this->_context;
        }

        $IPP = $Context->IPP();

        if ($realmID == null) {
            $realmID = $IPP->getRealmId();
        }

        switch ($IPP->version()) {
            case QuickBooks_IPP_IDS::VERSION_2:
                return $this->_add_v2($Context, $realmID, $resource, $Object);
            case QuickBooks_IPP_IDS::VERSION_3:
                return $this->_add_v3($Context, $realmID, $resource, $Object);
        }
    }

    protected function _add_v3(\QuickBooks_IPP_Context $Context, $realmID, $resource, $Object)
    {
        $IPP = $Context->IPP();

        $unsets = array(
            'Id',
        );

        foreach ($unsets as $unset) {
            $Object->remove($unset);
        }

        // Generate the XML
        $xml = $Object->asXML(0, null, null, null, QuickBooks_IPP_IDS::VERSION_3);

        // Send the data to IPP
        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_ADD, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK) {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    protected function _add_v2(\QuickBooks_IPP_Context $Context, $realmID, $resource, $Object)
    {
        $IPP = $Context->IPP();

        $unsets = array(
            'Id',
            'SyncToken',
            'MetaData',
            'ExternalKey',
            'Synchronized',
            'PartyReferenceId',
            'SalesTaxCodeId', // @todo These are customer/vendor specific and probably shouldn't be here
            'SalesTaxCodeName',
            'OpenBalanceDate',
            'OpenBalance',
        );

        foreach ($unsets as $unset) {
            $Object->remove($unset);
        }

        if ($IPP->flavor() == QuickBooks_IPP_IDS::FLAVOR_DESKTOP) {
            // Build the XML request
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
            $xml .= '<Add xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '" ' . QUICKBOOKS_CRLF;
            $xml .= '	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . QUICKBOOKS_CRLF;
            $xml .= '	RequestId="' . md5(mt_rand() . microtime()) . '" ' . QUICKBOOKS_CRLF;
            $xml .= '	xsi:schemaLocation="http://www.intuit.com/sb/cdm/' . $IPP->version() . ' ./RestDataFilter.xsd ">' . QUICKBOOKS_CRLF;
            $xml .= '	<OfferingId>ipp</OfferingId>' . QUICKBOOKS_CRLF;
            $xml .= '	<ExternalRealmId>' . $realmID . '</ExternalRealmId>' . QUICKBOOKS_CRLF;
            $xml .= '' . $Object->asIDSXML(1, null, QuickBooks_IPP_IDS::OPTYPE_ADD);
            $xml .= '</Add>';
        } else {
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . QUICKBOOKS_CRLF;
            $xml .= $Object->asIDSXML(0, null, QuickBooks_IPP_IDS::OPTYPE_ADD, $IPP->flavor());
        }

        // Send the data to IPP
        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_ADD, $xml);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK) {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    /**
     * @deprecated            Use _update() instead
     */
    protected function _modify(\QuickBooks_IPP_Context $Context, $realmID, $resource, $Object, $ID)
    {
        return $this->_update($Context, $realmID, $resource, $Object, $ID);
    }

    /**
     * Update an object within IDS (QuickBooks)
     *
     * @param QuickBooks_IPP_Context $Context
     * @param string                 $realmID
     * @param string                 $resource
     * @param object                 $Object
     * @param                        $ID
     *
     * @return boolean
     */
    protected function _update(\QuickBooks_IPP_Context $Context, $realmID, $resource, $Object, $ID)
    {
        $IPP = $Context->IPP();

        // Remove crap that we don't want to send to QuickBooks
        $unsets = array(
            'MetaData',
            'ExternalKey',
            'Synchronized',
            'CustomField',
            //	'PartyReferenceId',
            //	'SalesTaxCodeId', 		// @todo These are customer/vendor specific and probably shouldn't be here
            //	'SalesTaxCodeName',
            //	'OpenBalanceDate',
            //	'OpenBalance',
        );

        foreach ($unsets as $unset) {
            $Object->remove($unset);
        }

        $Object->set('Synchronized', 'false');

        if ($IPP->flavor() == QuickBooks_IPP_IDS::FLAVOR_DESKTOP) {
            // Build the XML request
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
            $xml .= '<Mod xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '" ' . QUICKBOOKS_CRLF;
            $xml .= '	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . QUICKBOOKS_CRLF;
            $xml .= '	RequestId="' . md5(mt_rand() . microtime()) . '" ' . QUICKBOOKS_CRLF;
            $xml .= '	xsi:schemaLocation="http://www.intuit.com/sb/cdm/' . $IPP->version() . ' ./RestDataFilter.xsd ">' . QUICKBOOKS_CRLF;
            $xml .= '	<ExternalRealmId>' . $realmID . '</ExternalRealmId>' . QUICKBOOKS_CRLF;
            $xml .= '' . $Object->asXML(1, null, QuickBooks_IPP_IDS::OPTYPE_MOD, $IPP->flavor(), $IPP->version());
            $xml .= '</Mod>';
        } else {
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . QUICKBOOKS_CRLF;
            $xml .= $Object->asXML(0, null, QuickBooks_IPP_IDS::OPTYPE_MOD, $IPP->flavor(), $IPP->version());
        }

        // Send the data to IPP
        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_MOD, $xml, $ID);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        // Check for errors
        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK) {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    /**
     * @param QuickBooks_IPP_Context $Context
     * @param string                 $realmID
     * @param string                 $resource
     * @param string|integer         $IDType
     * @param string                 $xml_or_IDType
     * @param null|string            $query
     *
     * @return null
     */
    protected function _findById(\QuickBooks_IPP_Context $Context, $realmID, $resource, $IDType, $xml_or_IDType = '', $query = null)
    {
        $IPP = $Context->IPP();

        $flavor = $IPP->flavor();

        if (!$xml_or_IDType) {
            if ($flavor == QuickBooks_IPP_IDS::FLAVOR_DESKTOP) {
                $parse = QuickBooks_IPP_IDS::parseIDType($IDType);

                $xml_or_IDType = '';
                $xml_or_IDType .= '<?xml version="1.0" encoding="UTF-8"?>' . QUICKBOOKS_CRLF;
                $xml_or_IDType .= '<' . $resource . 'Query xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.intuit.com/sb/cdm/' . $IPP->version() . '">' . QUICKBOOKS_CRLF;

                if ($resource == QuickBooks_IPP_IDS::RESOURCE_CUSTOMER) {
                    $xml_or_IDType .= '<CustomFieldEnable>true</CustomFieldEnable>';
                }

                if ($query) {
                    $xml_or_IDType .= $query;
                }

                $xml_or_IDType .= '	<' . QuickBooks_IPP_IDS::resourceToKeyType($resource) . 'Set>' . QUICKBOOKS_CRLF;
                $xml_or_IDType .= '		<Id idDomain="' . $parse['domain'] . '">' . $parse['ID'] . '</Id>' . QUICKBOOKS_CRLF;
                $xml_or_IDType .= '	</' . QuickBooks_IPP_IDS::resourceToKeyType($resource) . 'Set>' . QUICKBOOKS_CRLF;
                $xml_or_IDType .= '</' . $resource . 'Query>';
            } else {
                if ($flavor == QuickBooks_IPP_IDS::FLAVOR_ONLINE) {
                    $xml_or_IDType = $IDType;
                }
            }
        }

        $return = $IPP->IDS($Context, $realmID, $resource, QuickBooks_IPP_IDS::OPTYPE_FINDBYID, $xml_or_IDType);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if (count($return)) {
            return $return;
        }

        return null;
    }

    /**
     * @param QuickBooks_IPP_Context    $Context
     * @param                           $realmID
     * @param string                    $query
     *
     * @return bool|\Array
     */
    protected function _query(\QuickBooks_IPP_Context $Context, $realmID, $query)
    {
        $IPP = $Context->IPP();

        // Send the data to IPP
        $return = $IPP->IDS($Context, $realmID, null, QuickBooks_IPP_IDS::OPTYPE_QUERY, $query);
        $this->_setLastRequestResponse($Context->lastRequest(), $Context->lastResponse());
        $this->_setLastDebug($Context->lastDebug());

        if ($IPP->errorCode() != QuickBooks_IPP::ERROR_OK) {
            $this->_setError(
                $IPP->errorCode(),
                $IPP->errorText(),
                $IPP->errorDetail());

            return false;
        }

        return $return;
    }

    /**
     * Set the last XML request and XML response that was made by this service
     *
     * @param string $request  The last XML request that was made
     * @param string $response The last XML response that was made
     *
     * @return void
     */
    protected function _setLastRequestResponse($request, $response)
    {
        $this->_last_request  = $request;
        $this->_last_response = $response;
    }

    protected function _setLastDebug($debug)
    {
        $this->_last_debug = $debug;
    }

    /**
     * Get the last XML request that was made
     *
     * @param object $Context
     *
     * @return string
     */
    public function lastRequest(\QuickBooks_IPP_Context $Context = null)
    {
        if ($Context) {
            return $Context->lastRequest();
        }

        return $this->_last_request;
    }

    /**
     * Get the last raw XML response that was returned
     *
     * @param \QuickBooks_IPP_Context $Context If you provide a specific context, this will return the last response using that particular context, otherwise it will return the last response from this service
     *
     * @return string                The last raw XML response
     */
    public function lastResponse(\QuickBooks_IPP_Context $Context = null)
    {
        if ($Context) {
            return $Context->lastResponse();
        }

        return $this->_last_response;
    }

    public function lastError(\QuickBooks_IPP_Context $Context = null)
    {
        if ($Context) {
            return $Context->lastError();
        }

        return $this->_errcode . ': [' . $this->_errtext . ', ' . $this->_errdetail . ']';
    }

    public function lastDebug(\QuickBooks_IPP_Context $Context = null)
    {
        if ($Context) {
            return $Context->lastDebug();
        }

        return $this->_last_debug;
    }

    /**
     * Get the error number of the last error that occured
     *
     * @return mixed        The error number (or error code, some QuickBooks error codes are hex strings)
     */
    public function errorCode()
    {
        return $this->_errcode;
    }

    /**
     * Alias if ->errorCode()   (here for consistency with rest of framework)
     */
    public function errorNumber()
    {
        return $this->errorCode();
    }

    /**
     * Get the last error message that was reported
     *
     * Remember that issuing new commands may cause previous unchecked errors
     * to be *cleared*, so make sure you check for errors if you expect an
     * error might occur!
     *
     * @return string
     */
    public function errorText()
    {
        return $this->_errtext;
    }

    /**
     * Alias of ->errorText()   (here for consistency with rest of framework)
     */
    public function errorMessage()
    {
        return $this->errorText();
    }

    /**
     *  Get the error detail message from the response
     *
     * The error detail node sometimes contains additional information about
     * the error that occurred. You should make sure to also check the result
     * of ->errorCode() and ->errorMessage() too.
     *
     * @return string
     */
    public function errorDetail()
    {
        return $this->_errdetail;
    }

    /**
     * Tell whether or not an error occurred
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->_errcode != QuickBooks_IPP::ERROR_OK;
    }

    /**
     * Set an error message
     *
     * @param integer $errcode The error number/code
     * @param string  $errtext The text error message
     * @param string  $errdetail
     *
     * @return void
     */
    protected function _setError($errcode, $errtext = '', $errdetail = '')
    {
        $this->_errcode   = $errcode;
        $this->_errtext   = $errtext;
        $this->_errdetail = $errdetail;
    }
}
