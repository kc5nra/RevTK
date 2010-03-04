<?php
/**
 * View configuration file for all actions in this module.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
	'login' => array
	(
		'title'	      	   => 'Sign In - Reviewing the Kanji',
		'metas'            => array(
			'robots' => 'noindex,follow'
		),

		'javascripts' => array
		(
		    '/js/1.0/toolbox.js' => array('position' => 'first')
	    )
	),
	
	'contact' => array
	(
		'title'	      	   => 'Contact',
		'metas'            => array(
			'robots' => 'noindex,follow'
		),

		'javascripts' => array
		(
		    '/js/1.0/toolbox.js' => array('position' => 'first')
	    )
	),
	
	'memberslist' => array
	(
		'title'	      	   => 'Who\'s reviewing? - Reviewing the Kanji',
		'metas'            => array(
			'robots' => 'noindex, nofollow'
		),
		'stylesheets' => array
	  (
			'/css/2.0/widgets.css'
		),
		'javascripts' => array
		(
			'/js/lib/prototype.js',
			'/js/ui/uibase.js',
			'/js/ui/widgets.js'
		)
	)
);
