<?php

/**
 * File/class loader for QuickBooks packages 
 * 
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 * 
 * @package QuickBooks
 * @subpackage Loader
 */

/**
 * Class QuickBooks_Loader
 *
 * Provides methods for loading QuickBooks library files and classes.
 */
class QuickBooks_Loader
{
    /**
     * Indicates whether auto loading should be used.
     * @var bool
     */
    static private $autoLoadEnabled = true;

    /**
     * Returns the value of the auto load enabled flag
     *
     * @return bool true if auto loading is enabled
     */
    static public function isAutoLoadEnabled() {
        return self::$autoLoadEnabled;
    }

    /**
     * Changes the value of the auto load enabled flag.
     *
     * @param bool  $autoLoadEnabled    The new value of the auto load flag
     *
     * @return bool the previous value of auto load enabled flag
     */
    static public function setAutoLoadEnabled($autoLoadEnabled) {
        $previousValue = self::isAutoLoadEnabled();

        self::$autoLoadEnabled = $autoLoadEnabled;

        return $previousValue;
    }

    /**
     * Loads the requested file.
     *
     * @param string    $file       path of the file to be loaded
     * @param bool      $autoload   if true, then file is being autoloaded.
     *
     * @return bool always true.
     */
    static public function load($file, $autoload = true)
	{
		if ($autoload and
			self::registerAutoloader())
		{
			return true;
		}
		
		static $loaded = array();
		
		if (isset($loaded[$file]))
		{
			return true;
		}
		
		$loaded[$file] = true;
		
    	require_once(QUICKBOOKS_BASEDIR . $file);

		return true;
	}

    /**
     * Registers the auto load function for the QuickBooks library.
     *
     * @return bool true if successful, false otherwise
     */
    static protected function registerAutoloader()
	{
		if (self::$autoLoadEnabled)
		{
			return false;
		}
		
		static $done = false;
		static $auto = false;
		
		if (!$done)
		{
			$done = true;
			
			if (function_exists('spl_autoload_register'))
			{
				// Register the autoloader, and return TRUE
				spl_autoload_register(array( 'QuickBooks_Loader', '__autoload' ));
				
				$auto = true;
				return true;
			}
		}
		
		return $auto;
	}

    /**
     * The autoload handler for the QuickBooks library
     *
     * @param string $name  The file to be loaded.
     */
    static public function __autoload($name)
	{
		if (substr($name, 0, 10) == 'QuickBooks')
		{
			$file = '/' . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
			QuickBooks_Loader::load($file, false);
		}
	}
	
	/** 
	 * Import (require_once) a bunch of PHP files from a particular PHP directory
	 * 
	 * @param string $dir
	 * @return boolean
	 */

    /**
     * Load all files in the specified directory.
     *
     * @param string    $dir
     * @param bool      $autoload
     *
     * @return bool     true is successful, false otherwise.
     */
    static public function import($dir, $autoload = true)
	{
		$dh = opendir(QUICKBOOKS_BASEDIR . $dir);
		if ($dh)
		{
			while (false !== ($file = readdir($dh)))
			{
				$tmp = explode('.', $file);
				if (end($tmp) == 'php' and 
					!is_dir(QUICKBOOKS_BASEDIR . $dir . DIRECTORY_SEPARATOR . $file))
				{
					QuickBooks_Loader::load($dir . DIRECTORY_SEPARATOR . $file, $autoload);
				}
			}

            closedir($dh);
			return true;
		}
		
		return false;
	}	
}
