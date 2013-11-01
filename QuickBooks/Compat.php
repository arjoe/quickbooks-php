<?php

/** 
 * Compatibility functions for the QuickBooks library
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
 */

if (!function_exists('array_intersect_key'))
{
    /**
     * Returns an array containing all the entries of array1 which have keys that are present in all the arguments.
     *
     * @param array $array1 The array with master keys to check.
     * @param array $array2 An array to compare keys against.
     *
     * @return array An associative array containing all the entries of array1 which have keys that are present in all arguments.
     */
    function array_intersect_key(array $array1, array $array2)
	{
		$argc = func_num_args();
		if ($argc > 2)
		{
			for ($i = 1; !empty($array1) && $i < $argc; $i++)
			{
				$arr = func_get_arg($i);
				foreach (array_keys($array1) as $key)
				{
					if (!isset($arr[$key]))
					{
						unset($array1[$key]);
					}
				}
			}
			
			return $array1;
		}
		else
		{
			$res = array();
			foreach (array_keys($array1) as $key)
			{
				if (isset($array2[$key]))
				{
					$res[$key] = $array1[$key];
				}
			}
			return $res;
		}
	}
}