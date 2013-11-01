<?php

/**
 * Static callback methods for the API server
 * 
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 * 
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt 
 * 
 * @package QuickBooks
 * @subpackage Callbacks
 */

/**
 * QuickBooks utility methods
 */
QuickBooks_Loader::load('/QuickBooks/Utilities.php');

/**
 * QuickBooks XML parser class 
 */
QuickBooks_Loader::load('/QuickBooks/XML/Parser.php');

/**
 * QuickBooks object instance classes
 */
QuickBooks_Loader::load('/QuickBooks/Object.php');

/**
 * 
 */
QuickBooks_Loader::load('/QuickBooks/Callbacks.php');

/**
 * Static callback methods for the API classes
 */
class QuickBooks_Callbacks_API_Callbacks
{
	/**
	 *
	 * @param string $xml
	 * @param string $version
	 * @param string $locale
     * @param string $requestID
	 * @param string $onerror
	 * @return string
	 */
	protected static function _replacements($xml, $version, $locale, $requestID, $onerror = 'stopOnError')
	{
		if ($locale and $locale != QUICKBOOKS_LOCALE_US)
		{
			$version = $locale . $version;
		}
		
		$xml = str_replace('{$version}', $version, $xml);
		$xml = str_replace('{$locale}', $locale, $xml);
		$xml = str_replace('{$requestID}', $requestID, $xml);
		$xml = str_replace('{$onerror}', $onerror, $xml);
		
		return $xml;
	}
	
	
	/**
	 * Create a mapping between an application's primary key and a QuickBooks object
	 *
     * @param $func
     * @param $user
	 * @param string $type				The type of QuickBooks object (i.e.: QUICKBOOKS_OBJECT_CUSTOMER, QUICKBOOKS_OBJECT_INVOICE, etc.)
	 * @param mixed $ID					The primary key of the application record
	 * @param string $ListID_or_TxnID	The ListID or TxnID of the object within QuickBooks
	 * @param string $editsequence		The EditSequence of the object within QuickBooks
     * @param $extra
     *
	 * @return boolean					
	 */
	protected static function _mapCreate($func, $user, $type, $ID, $ListID_or_TxnID, $editsequence = '', $extra = null)
	{
		if (strlen($func))
		{
			if (false === strpos($func, '::'))
			{
				return $func($type, $ID, $ListID_or_TxnID, $editsequence, $extra); 
			}
			else
			{
				$tmp = explode('::', $func);
				
				return call_user_func(array( $tmp[0], $tmp[1] ), $type, $ID, $ListID_or_TxnID, $editsequence, $extra);
			}
		}
		else
		{ 
			$Driver = QuickBooks_Driver_Singleton::getInstance();
			return $Driver->identMap($user, $type, $ID, $ListID_or_TxnID, $editsequence, $extra);
		}
	}
	
	/**
	 * Map an application primary key to a QuickBooks ListID or TxnID
	 *
     * @param $func
     * @param $user
	 * @param string $type		The type of object (i.e.: QUICKBOOKS_OBJECT_CUSTOMER, QUICKBOOKS_OBJECT_INVOICE, etc.)
	 * @param mixed $ID			The primary key of the record
     *
	 * @return string			The ListID or TxnID (or NULL if it couldn't be mapped)			
	 */
	protected static function _mapToQuickBooksID($func, $user, $type, $ID)
	{
		if (strlen($func))
		{
			if (false === strpos($func, '::'))
			{
				return $func($type, $ID); 
			}
			else
			{
				$tmp = explode('::', $func);
				
				return call_user_func(array( $tmp[0], $tmp[1] ), $type, $ID);
			}
		}
		else
		{
			$editsequence = '';
			$extra = null;
			
			$Driver = QuickBooks_Driver_Singleton::getInstance();
			$ListID_or_TxnID = $Driver->identToQuickBooks($user, $type, $ID, $editsequence, $extra);
			
			return $ListID_or_TxnID;
		}
	}
	
	/**
	 * Map a QuickBooks ListID or TxnID to an application primary key
	 *
     * @param $func
     * @param $user
	 * @param string $type					The type of object
	 * @param string $ListID_or_TxnID		The ListID or TxnID of the object within QuickBooks
     *
	 * @return string						The application record primary key
	 */
	static protected function _mapToApplicationID($func, $user, $type, $ListID_or_TxnID)
	{
		if (strlen($func))
		{
			if (false === strpos($func, '::'))
			{
				return $func($type, $ListID_or_TxnID); 
			}
			else
			{
				$tmp = explode('::', $func);
				
				return call_user_func(array( $tmp[0], $tmp[1] ), $type, $ListID_or_TxnID);
			}
		}
		else
		{
			$extra = null;
			
			$Driver = QuickBooks_Driver_Singleton::getInstance();
			return $Driver->identToApplication($user, $type, $ListID_or_TxnID, $extra);
		}
	}
	
	/**
	 * Map a type and application primary key to a QuickBooks EditSequence string
	 *
     * @param $func
     * @param $user
	 * @param string $type		The type of object
	 * @param mixed $ID			The application primary key
	 * @return string			The QuickBooks EditSequence string
	 */
	static protected function _mapToEditSequence($func, $user, $type, $ID)
	{
		if (strlen($func))
		{
			if (false === strpos($func, '::'))
			{
				return $func($type, $ID); 
			}
			else
			{
				$tmp = explode('::', $func);
				
				return call_user_func(array( $tmp[0], $tmp[1] ), $type, $ID);
			}
		}
		else
		{
			$editsequence = '';
			$extra = null;
			
			$Driver = QuickBooks_Driver_Singleton::getInstance();
			$Driver->identToQuickBooks($user, $type, $ID, $editsequence, $extra);
			
			return $editsequence;
		}
	}
	
	/**
	 * @TODO THIS NEEDS SOME SERIOUS CLEANUP
	 */	
	public static function mappings($xml, $user, $config = array())
	{
		return QuickBooks_Callbacks_API_Callbacks::_mappings($xml, $user, $config);
	}
	
	/**
	 * 
	 * @TODO THIS NEEDS SOME SERIOUS CLEANUP
	 * 
	 * @param string $xml
     * @param $user
     * @param $config
     *
	 * @return string
	 */
	protected static function _mappings($xml, $user, $config)
	{
		if (empty($config['map_to_quickbooks_handler']))
		{
			$config['map_to_quickbooks_handler'] = null;
		}
		
		while (false !== ($start = strpos($xml, '<' . QUICKBOOKS_API_APPLICATIONID . '>')))
		{
			$end = strpos($xml, '</' . QUICKBOOKS_API_APPLICATIONID . '>');
			
			$encode = substr($xml, $start + strlen(QUICKBOOKS_API_APPLICATIONID) + 2, $end - $start - strlen(QUICKBOOKS_API_APPLICATIONID) - 2);
			
			$type = '';
			$tag = '';
			$ID = '';
			QuickBooks_Callbacks_API_Callbacks::_decodeApplicationID($encode, $type, $tag, $ID);
			
			$ListID_or_TxnID = QuickBooks_Callbacks_API_Callbacks::_mapToQuickBooksID($config['map_to_quickbooks_handler'], $user, $type, $ID);

			$xml = substr($xml, 0, $start) . '<' . $tag . '>' . $ListID_or_TxnID . '</' . $tag . '>' . substr($xml, $end + strlen(QUICKBOOKS_API_APPLICATIONID) + 3);
		}
		
		$start = strpos($xml, '<' . QUICKBOOKS_API_APPLICATIONEDITSEQUENCE . '>');
		$end = strpos($xml, '</' . QUICKBOOKS_API_APPLICATIONEDITSEQUENCE . '>');
		
		if ($start and $end)
		{
			$encode = substr($xml, $start + strlen(QUICKBOOKS_API_APPLICATIONEDITSEQUENCE) + 2, $end - $start - strlen(QUICKBOOKS_API_APPLICATIONEDITSEQUENCE) - 2);
			
			$type = '';
			$tag = '';
			$ID = '';
			QuickBooks_Callbacks_API_Callbacks::_decodeApplicationEditSequence($encode, $type, $tag, $ID);
			
			$EditSequence = QuickBooks_Callbacks_API_Callbacks::_mapToEditSequence($config['map_to_quickbooks_handler'], $user, $type, $ID);
			
			$xml = substr($xml, 0, $start) . '<EditSequence>' . $EditSequence . '</EditSequence>' . substr($xml, $end + strlen(QUICKBOOKS_API_APPLICATIONEDITSEQUENCE) + 3);
		}

		return $xml;
	}
	
	protected static function _decodeApplicationID($encode, &$type, &$tag, &$ID)
	{
		return QuickBooks_API::decodeApplicationID($encode, $type, $tag, $ID);
	}
	
	protected static function _decodeApplicationEditSequence($encode, &$type, &$tag, &$ID)
	{
		return QuickBooks_API::decodeApplicationEditSequence($encode, $type, $tag, $ID);
	}
	
	protected static function _defaults($options)
	{
		$defaults = array(
			'always_use_iterators' => false, 
			'map_create_handler' => null, 
			'map_to_quickbooks_handler' => null,
			'map_to_application_handler' => null,
			'map_to_editsequence_handler' => null,  
			);
		
		return array_merge($defaults, $options);
	}
	
	/**
	 * 
	 * @TODO This callback code should be ported to QuickBooks_Callbacks style calls
	 * 
	 * @param string $funcs_or_methods
	 * @param string $method
	 * @param string $action
	 * @param mixed $ID
	 * @param string $err
	 * @param string $qbxml
	 * @param object $qbobject
	 * @param resource $qbres
	 * @return boolean
	 */
	protected static function _callCallbacks($funcs_or_methods, $method, $action, $ID, &$err, $qbxml, $qbobject, $qbres)
	{
		foreach ($funcs_or_methods as $callback)
		{
			if (!$callback)
			{
				continue;
			}
			
			$return = QuickBooks_Callbacks::callAPICallback(null, null, $callback, 
				$method, 
				$action, 
				$ID, 
				$err, 
				$qbxml, 
				$qbobject, 
				$qbres);
			
			if (!$return)
			{
				break;
			}
		}
		
		if ($err)
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param float $version
	 * @param string $locale
	 * @param array $config
     * @param string $qbxml
     *
	 * @return string
	 */
	protected static function _doQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		if (isset($extra['api']))
		{
			if ($qbxml)
			{
				$qbxml = QuickBooks_Callbacks_API_Callbacks::_replacements($qbxml, $version, $locale, $requestID);
				
				return $qbxml;
			}
		}
		else
		{
			$err = 'Request ID ' . $requestID . ', ' . $action . ', ' . $ID . ' is not an API request...';
			return false;
		}
	}
	
	/**
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $xml
	 * @param array $idents
     * @param array $callback_options
	 * @return boolean
	 */
	protected static function _doQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $callback_options = array())
	{
		// This is stuff we'll be passing to the callback handler functions/methods
		// $action
		// $ID
		$err = '';
		$qbxml =& $xml;
		$qbiterator = null;
		$qbres = null;
		
		$method = null;
		if (isset($extra['method']))
		{
			$method = $extra['method'];
		}
		
		$xml_errnum = 0;
		$xml_errmsg = '';
		
		$Parser = new QuickBooks_XML_Parser($xml);
		if ($Parser->validate($xml_errnum, $xml_errmsg) and 
			$Doc = $Parser->parse($xml_errnum, $xml_errmsg))
		{
			$list = array();
			
			$Root = $Doc->getRoot();
			
			// Get rid of some gunk... 
			$Response = $Root->getChildAt('QBXML QBXMLMsgsRs ' . $action . 'Rs');
			
			if ($Response)
			{
				foreach ($Response->children() as $Child)
				{
					if ($Object = QuickBooks_Callbacks_API_Callbacks::_objectFromXML($action, $Child))
					{
						$list[] = $Object;
					}
				}
			}
			
			$Iterator = new QuickBooks_Iterator($list);
		}
		else
		{
			$err = 'XML parser error: ' . $xml_errnum . ': ' . $xml_errmsg;
		}
		
		if ($err)
		{
			return false;
		}
		
		if (isset($extra['callbacks']) and is_array($extra['callbacks']))
		{
			QuickBooks_Callbacks_API_Callbacks::_callCallbacks($extra['callbacks'], $method, $action, $ID, $err, $qbxml, $Iterator, $qbres, $callback_options);
		}
		
		return true;
	}
	
	protected static function _doModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	protected static function _doModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	protected static function _doAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		if (isset($extra['api']))
		{
			if ($qbxml)
			{
				$config = QuickBooks_Callbacks_API_Callbacks::_defaults($config);
				
				$qbxml = QuickBooks_Callbacks_API_Callbacks::_replacements($qbxml, $version, $locale, $requestID);
				$qbxml = QuickBooks_Callbacks_API_Callbacks::_mappings($qbxml, $user, $config);
				
				return $qbxml;
			}
			
			$err = 'API Server could not find any qbXML requests to send...';
			return false;
		}
		else
		{
			// this is *not* a request that was supposed to come from the API, 
			//	so, we'll re-queue it, and *not* process it 
			$err = 'Request ID ' . $requestID . ', ' . $action . ', ' . $ID . ' is not an API request...';
			return false;
		}
	}
	
	public static function RawQBXMLResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		// Determine the $action parameter (_doAddResponse needs this)
		// @TODO Move this to the _doAddRseponse method
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	protected static function _doAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		// This is stuff we'll be passing to the callback handler functions/methods
		$method = null;
		$err = '';
		$qbxml =& $xml;
		$qbobject = null;
		$qbres = null;
		
		if (isset($extra['method']))
		{
			$method = $extra['method'];
		}
		
		$xml_errnum = 0;
		$xml_errmsg = '';
		
		$Object = null;
		
		if ($action)
		{
			$Parser = new QuickBooks_XML_Parser($xml);
			if ($Parser->validate($xml_errnum, $xml_errmsg) and 
				$Doc = $Parser->parse($xml_errnum, $xml_errmsg))
			{
				$Root = $Doc->getRoot();
				
				// There is some nested garbage we don't really need here... let's get rid of it
				$Response = $Root->getChildAt('QBXML QBXMLMsgsRs ' . $action . 'Rs');
				
				$Child = null;
				if ($Response)
				{
					$Child = $Response->getChild(0);
					
					// Try to build an object from the returned XML
					if ($Object = QuickBooks_Callbacks_API_Callbacks::_objectFromXML($action, $Child))
					{
						; 
					}
					else
					{
						$Object = null;
					}
				}
			}
			else
			{
				$err = 'XML parser error: ' . $xml_errnum . ': ' . $xml_errmsg;
			}
		}
		
		if ($err)
		{
			return false;
		}
		
		if (isset($extra['callbacks']) and is_array($extra['callbacks']))
		{
			QuickBooks_Callbacks_API_Callbacks::_callCallbacks($extra['callbacks'], $method, $action, $ID, $err, $qbxml, $Object, $qbres);
		}
				
		return true;
	}
	
	/**
	 * 
	 * 
	 * 
	 */	
	protected static function _objectFromXML($action, $XML)
	{
		return QuickBooks_Object::fromXML($XML, $action);
	}
	
	public static function AccountAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function AccountAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ClassAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ClassAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function EmployeeAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function EmployeeAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function EstimateAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function EstimateAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function BillAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function BillAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function BillPaymentCheckAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function BillPaymentCheckAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function CheckAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function CheckAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function DepositAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function DepositAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function JournalEntryAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function JournalEntryAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	

	public static function InventoryAdjustmentAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}

	public static function InventoryAdjustmentAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function InvoiceAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function InvoiceAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function ItemReceiptAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}

	public static function ItemReceiptAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function SalesReceiptAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function SalesReceiptAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function VendorAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function VendorAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemServiceAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemServiceAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function ItemSalesTaxAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemSalesTaxAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemInventoryAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemInventoryAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemNonInventoryAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemNonInventoryAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ReceivePaymentAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ReceivePaymentAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	
	public static function CustomerAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function CustomerAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function CustomerModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doModRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function CustomerModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doModResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function DataExtAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function DataExtAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function DataExtModRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doModRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function DataExtModResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doModResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function BillPaymentCheckQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function BillPaymentCheckQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function BillQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function BillQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function BillingRateQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function BillingRateQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function CheckQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function CheckQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function JournalEntryQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function JournalEntryQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	/**
	 * Pass a request to QuickBooks
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $version
	 * @param array $locale
     * @param array $config
     * @param string $qbxml
     *
	 * @return boolean
	 */
	public static function PaymentMethodQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	/**
	 * Handle a response from QuickBooks
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $xml
	 * @param array $idents
	 * @return boolean
	 */
	public static function PaymentMethodQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ChargeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ChargeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ClassQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ClassQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	
	public static function CustomerQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function CustomerQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function CustomerTypeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function CustomerTypeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function EmployeeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function EmployeeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function EstimateQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function EstimateQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function InvoiceQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function InvoiceQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	
	/**
	 * Pass a request to QuickBooks
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $version
	 * @param array $locale
     * @param array $config
     * @param string $qbxml
     *
	 * @return boolean
	 */
	public static function ItemQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	/**
	 * Handle a response from QuickBooks
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $xml
	 * @param array $idents
	 * @return boolean
	 */
	public static function ItemQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemServiceQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemServiceQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemSalesTaxQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemSalesTaxQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemSalesTaxGroupQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemSalesTaxGroupQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function SalesTaxCodeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function SalesTaxCodeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}

	public static function ItemInventoryQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemInventoryQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ItemNonInventoryQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ItemNonInventoryQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function SalesOrderQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function SalesOrderQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function SalesReceiptQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function SalesReceiptQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	/**
	 * Pass a request to QuickBooks
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $version
	 * @param array $locale
     * @param array $config
     * @param string $qbxml
     *
	 * @return boolean
	 */
	public static function ShipMethodAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	/**
	 * Handle a response from QuickBooks
	 * 
	 * @param string $requestID
	 * @param string $user
	 * @param string $action
	 * @param mixed $ID
	 * @param mixed $extra
	 * @param string $err
	 * @param integer $last_action_time
	 * @param integer $last_actionident_time
	 * @param string $xml
	 * @param array $idents
	 * @return boolean
	 */
	public static function ShipMethodAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doAddResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function ShipMethodQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function ShipMethodQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	

	public static function UnitOfMeasureSetQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function UnitOfMeasureSetQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}	
	
	public static function AccountQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryRequest($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $version, $locale, $config, $qbxml);
	}
	
	public static function AccountQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		return QuickBooks_Callbacks_API_Callbacks::_doQueryResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public static function TermsQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		
	}
	
	public static function TermsQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		
	}
	
	public static function VendorQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		
	}
	
	public static function VendorQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		
	}
	
	public static function TermsTypeQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		
	}
	
	public static function VendorTypeQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		
	}
	
	public static function VendorCreditQueryQueryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale, $config = array(), $qbxml = null)
	{
		
	}
	
	public static function VendorCreditQueryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		
	}
}
