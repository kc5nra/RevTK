<?php
/**
 * 
 * 
 * @author     Fabrice Denis
 */
 
class demoValidators
{
	/**
	 * Example callback validator,
	 * checks that the value does not contain html tags.
	 * 
	 * @return bool  true if validates, false otherwise
	 */
	public static function validateTextarea($value)
	{
		return  (strip_tags($value)==$value);
	}
}
