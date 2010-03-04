<?php
/**
 * Provides utility functions.
 * 
 * @author     Fabrice Denis
 * @package    Core
 * @subpackage Toolkit
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

class coreToolkit
{
	/**
	 * Load helpers.
	 * 
   * @param  array  $helpers     An array of helpers to load
   * 
   * @throws coreException
	 */
	static public function loadHelpers($helpers)
	{
		static $loaded = array();

		// directories
		$dirs = array();
		$dirs[] = coreConfig::get('app_lib_dir').'/helper';
		$dirs[] = coreConfig::get('lib_dir').'/helper';
		$dirs[] = coreConfig::get('core_dir').'/helper';

		foreach((array)$helpers as $helperName)
		{
			if (isset($loaded[$helperName]))
			{
				continue;
			}
			
			$fileName = $helperName.'Helper.php';
			foreach ($dirs as $dir)
			{
				$included = false;
				if (is_readable($dir.'/'.$fileName))
				{
					include_once($dir.'/'.$fileName);
					$included = true;
					break;
				}
			}

			if (!$included)
			{
				throw new coreException(sprintf('Unable to load "%sHelper.php" helper in: %s.', $helperName, implode(', ', $dirs)));
			}

			$loaded[$helperName] = true;
		}
	}
	
	/**
	 * Returns true if gzip encoding is available on user agent.
	 * 
	 * @return boolean
	 */
	static public function detectGzipEncodingSupport()
	{
		// don't use gzip compression on IE6 SP1
		// @see hotfix  http://support.microsoft.com/default.aspx?scid=kb;en-us;823386&Product=ie600)
		// Fabrice: no means to truly test this, so screw ie6!
		/*
	  $IE6bug = false;
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
		  $ua = $_SERVER['HTTP_USER_AGENT'];
		  $IE6bug = (strpos($ua, 'MSIE 6') && strpos($ua, 'Opera') == -1);
		}*/
	
		// For some very odd reason, "Norton Internet Security" unsets this
		$_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
	
		if (extension_loaded('zlib') && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false))
		{
			return true;
		}

		return false;
	}

	/**
	 * Determine if a filesystem path is absolute.
	 *
	 * @param	path $path	A filesystem path.
	 *
	 * @return bool true, if the path is absolute, otherwise false.
	 */
	public static function isPathAbsolute($path)
	{
		if ($path[0] == '/' || $path[0] == '\\' ||
			(strlen($path) > 3 && ctype_alpha($path[0]) &&
			 $path[1] == ':' &&
			 ($path[2] == '\\' || $path[2] == '/')
			)
		)
		{
			return true;
		}

		return false;
	}

	/**
	 * Strip slashes recursively from array
	 * 
	 * @param array  the value to strip
	 * @return array clean value with slashes stripped
	 */
	static public function stripslashesDeep($value)
	{
		return is_array($value) ? array_map(array('coreToolkit', 'stripslashesDeep'), $value) : stripslashes($value);
	}

	/**
	 * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
	 *
	 * Like array_merge
	 *
	 *	arrayDeepMerge() merges the elements of one or more arrays together so
	 * that the values of one are appended to the end of the previous one. It
	 * returns the resulting array.
	 *	If the input arrays have the same string keys, then the later value for
	 * that key will overwrite the previous one. If, however, the arrays contain
	 * numeric keys, the later value will not overwrite the original value, but
	 * will be appended.
	 *	If only one array is given and the array is numerically indexed, the keys
	 * get reindexed in a continuous way.
	 *
	 * Different from array_merge
	 *	If string keys have arrays for values, these arrays will merge recursively.
	 *
	 * @author  Code from php at moechofe dot com (array_merge comment on php.net)
	 */
	public static function arrayDeepMerge()
	{
		switch (func_num_args())
		{
			case 0:
				return false;
			case 1:
				return func_get_arg(0);
			case 2:
				$args = func_get_args();
				$args[2] = array();
				if (is_array($args[0]) && is_array($args[1]))
				{
					foreach (array_unique(array_merge(array_keys($args[0]),array_keys($args[1]))) as $key)
					{
						$isKey0 = array_key_exists($key, $args[0]);
						$isKey1 = array_key_exists($key, $args[1]);
						if ($isKey0 && $isKey1 && is_array($args[0][$key]) && is_array($args[1][$key]))
						{
							$args[2][$key] = self::arrayDeepMerge($args[0][$key], $args[1][$key]);
						}
						else if ($isKey0 && $isKey1)
						{
							$args[2][$key] = $args[1][$key];
						}
						else if (!$isKey1)
						{
							$args[2][$key] = $args[0][$key];
						}
						else if (!$isKey0)
						{
							$args[2][$key] = $args[1][$key];
						}
					}
					return $args[2];
				}
				else
				{
					return $args[1];
				}
			default :
				$args = func_get_args();
				$args[1] = sfToolkit::arrayDeepMerge($args[0], $args[1]);
				array_shift($args);
				return call_user_func_array(array('coreToolkit', 'arrayDeepMerge'), $args);
				break;
		}
	}

	/**
	* Converts string to array
	* 
	* @param  string $string  the value to convert to array
	* 
	* @return array
	*/
	static public function stringToArray($string)
	{
		preg_match_all('/
		  \s*(\w+)              # key                               \\1
		  \s*=\s*               # =
		  (\'|")?               # values may be included in \' or " \\2
		  (.*?)                 # value                             \\3
		  (?(2) \\2)            # matching \' or " if needed        \\4
		  \s*(?:
		    (?=\w+\s*=) | \s*$  # followed by another key= or the end of the string
		  )
		/x', $string, $matches, PREG_SET_ORDER);
		
		$attributes = array();
		foreach ($matches as $val)
		{
		  $attributes[$val[1]] = self::literalize($val[3]);
		}
		
		return $attributes;
	}

	/**
	 * Finds the type of the passed value, returns the value as the new type.
	 *
	 * @param	string
	 * @param	boolean Quote?
	 *
	 * @return mixed
	 */
	public static function literalize($value, $quoted = false)
	{
		// lowercase our value for comparison
		$value	= trim($value);
		$lvalue = strtolower($value);

		if (in_array($lvalue, array('null', '~', '')))
		{
			$value = null;
		}
		else if (in_array($lvalue, array('true', 'on', '+', 'yes')))
		{
			$value = true;
		}
		else if (in_array($lvalue, array('false', 'off', '-', 'no')))
		{
			$value = false;
		}
		else if (ctype_digit($value))
		{
			$value = (int) $value;
		}
		else if (is_numeric($value))
		{
			$value = (float) $value;
		}
		else
		{
			$value = self::replaceConstants($value);
			if ($quoted)
			{
				$value = '\''.str_replace('\'', '\\\'', $value).'\'';
			}
		}

		return $value;
	}

	/**
	 * Replaces constant identifiers in a scalar value.
	 *
	 * @param string the value to perform the replacement on
	 *
	 * @return string the value with substitutions made
	 */
	public static function replaceConstants($value)
	{
		return is_string($value) ? preg_replace('/%(.+?)%/e', 'coreConfig::has(strtolower("\\1")) ? coreConfig::get(strtolower("\\1")) : "%\\1%"', $value) : $value;
	}
}
