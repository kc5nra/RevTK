<?php
/**
 * View configuration file for all actions in this module.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
	'index' => array
	(
	),
	
	'addcustom' => array
	(
		'title' => 'Manage Flashcards: Add custom selection'
	),

	'removelist' => array
	(
		'title' => 'Manage Flashcards: Remove from list',
		'stylesheets' => array(
			'/css/2.0/widgets.css'
		)
	),

	'all' => array
	(
		'title'	      	   => 'Manage Flashcards',
		'javascripts'      => array
		(
			'/js/lib/prototype.min.js',
			'/js/ui/uibase.js',
			'/js/ui/widgets.js',
			'/js/2.0/manage/manage.js'
		)
	)
);
