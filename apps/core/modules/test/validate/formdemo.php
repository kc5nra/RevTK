<?php
/**
 * Example Validator configuration file, used by /test/formdemo
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

return array
(
	'fields' => array
	(
		'firstname' => array
		(
			'required' => array
			(
				'msg' 			=> 'Firstname can not be left blank'
			),
			'StringValidator' 	=> array
			(
				'min' 			=> 3,
				'min_error' 	=> 'Firstname is too short (min 3 characters)',
				'max' 			=> 10,
				'max_error'		=> 'Firstname is too long (max 10 characters)'
			),
			'RegexValidator' 	=> array
			(
				'match' 		=> true,
				'pattern' 		=> '/^[a-zA-Z ]+$/',
				'match_error' 	=> 'Firstname does not match regex'
			)
		),
		'age' => array
		(
			'NumberValidator' 	=> array
			(
				'nan_error' 	=> 'Please enter a numeric value',
				'type' 			=> 'int',
				'type_error' 	=> 'Please enter an integer number',
				'min' 			=> 1,
				'min_error' 	=> 'The value must be at least 1',
				'max' 			=> 99,
				'max_error' 	=> 'The value must be not greater than 99'
			)
		),
		'url' => array
		(
			'UrlValidator' 		=> array
			(
				'url_error' 	=> 'This URL is invalid'
			)
		),
		'email' => array
		(
			'EmailValidator' 	=> array
			(
				'strict' 		=> true,
				'email_error' 	=> 'Invalid email.'
			)
		),
		'textarea' => array
		(
			'CallbackValidator' => array
			(
				'callback' 		=> array('demoValidators', 'validateTextarea'),
				'invalid_error' => 'Description can not use HTML tags.'
			)
		),
		'password' => array
		(
			'required' 			=> array
			(
				'msg' 			=> 'Password can not be left blank'
			)
		),
		'verifypassword' => array
		(
			'required' 			=> array
			(
				'msg' 			=> 'Please retype the password'
			),
			'CompareValidator' 	=> array
			(
				'check' 		=> 'password',
				'compare_error' => 'The passwords don\'t match.'
			)
		)
	)
);
