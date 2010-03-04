<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Validators used throught the RevTK application.
 *
 * These should be used with request variables. Therefore even when validating
 * an integer, the value argument is always a string coming from GET/POST requests.
 * 
 * @package    RevTK
 * @subpackage Validators
 * @author     Fabrice Denis
 */

class rtkValidators
{
	/**
	 * Validate RevTK username.
	 * 
	 * @return 
	 * @param object $value
	 * @param object $params
	 */
	public static function validateUsernameChars($value)
	{
		return (preg_match('/^[a-zA-Z0-9_]+$/', $value) > 0);
	}

	/**
	 * Filter out meaningless and/or "l33t" usernames such as:
	 * 
	 * - all digits (eg. '01301324')
	 * - digit prefix (eg. '4ever')
	 * - using underscore decoration (eg. '_thepimp_')
	 * - using multiple underscores (eg. '__lalala')
	 * 
	 * @return boolean
	 * @param object $value
	 * @param object $params
	 */
	public static function validateUsernamePrefix($value)
	{
		return (preg_match('/^[0-9_]|_$|__/', $value)==0);
	}
	
	public static function validateNoHtmlTags($value)
	{
		return (strip_tags($value) == $value);
	}
}
