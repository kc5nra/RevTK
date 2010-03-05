<?php
/**
 * View configuration file for all actions in this module.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
	'create' => array
	(
		'title'	      	   => 'Register an Account - Reviewing the Kanji',
		'metas'            => array(
			'robots' => 'noindex,nofollow'
		),

		'javascripts' => array
		(
		    '/js/1.0/toolbox.js' => array('position' => 'first')
	    )
	),
	
	'forgotPassword' => array
	(
		'title'	      	   => 'Forgot your password?',
		'metas'            => array(
			'robots' => 'noindex,nofollow'
		),

		'javascripts' => array
		(
		    '/js/1.0/toolbox.js' => array('position' => 'first')
	    )
	),

	'password' => array
	(
		'title'	      	   => 'Change Password',
		'javascripts' => array
		(
		    '/js/1.0/toolbox.js' => array('position' => 'first')
	    )
	)
);
