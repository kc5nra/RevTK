<?php
/**
 * A collection of useful callback functions to be used in
 * coreValidator configuration files.
 *
 * @author     Fabrice Denis
 */

class BaseValidators
{
	/**
	 * Value can not be empty.
	 * 
	 */
	public static function validateNotEmpty($value)
	{
		return !empty($value) || ($value!=='0' && $value!==0);
	}

	/**
	 * Checks that every character is a digit (must be a positive integer).
	 * 
	 * @return 
	 * @param object $value
	 */
	public static function validateInteger($value)
	{
		return is_int($value) || ctype_digit($value);
	}
}
