<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Form Validation helpers.
 * 
 * @package    RevTK
 * @subpackage Helper
 * @author     Fabrice Denis
 */

/**
 * Output all validation errors.
 * 
 * @return 
 */
function form_errors()
{
	$request = coreContext::getInstance()->getRequest();

	$s = '';
	if($request->hasErrors())
	{
		foreach($request->getErrors() as $key => $message)
		{
			$s .= "<strong>$message</strong><br />\n";
		}
		$s = content_tag('div', $s, array('class' => 'formerrormessage'));
	}
	return $s;
}
