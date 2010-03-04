<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Feedback Form validation.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
	'fields' => array
	(
		'name' => array
		(
			'required' 			=> array
			(
				'msg' 			=> 'Please enter your name or nickname.'
			),
			'StringValidator' 	=> array
			(
				'max' 			=> 100,
				'max_error' 	=> 'Name is too long (max 100 characters).'
			),
		),
		'email' => array
		(
			'required' 			=> array
			(
				'msg' 			=> 'Please enter a valid email address so that I can reply to you.'
			),
			'EmailValidator' 	=> array
			(
				'strict' 		=> true,
				'email_error' 	=> 'Email is not valid.'
			),
			'StringValidator' 	=> array
			(
				'max' 			=> 100,
				'max_error' 	=> 'Email is too long (max 100 characters).'
			)
		),
		'message' => array
		(
			'required' 			=> array
			(
				'msg' 			=> 'Please enter your message.'
			),
			'CallbackValidator' => array
			(
				'callback'		=> array('rtkValidators', 'validateNoHtmlTags'),
				'invalid_error' => ''
			)
		),
	)
);
