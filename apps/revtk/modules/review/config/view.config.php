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
		'title'	      => 'Review Status',
		'stylesheets' => array
		(
			'/css/2.0/widgets.css',
			'/css/2.0/leitner-svg.css'
		),
		'javascripts' => array
		(
			'/js/lib/prototype.min.js',
			'/js/ui/uibase.min.js',
			'/js/ui/widgets.js',
			'/js/lib/raphael.min.js',
			'/js/2.0/review/rkLeitnerView.js'
		)
	),

	'flashcardlist' => array
	(
		'title'	      => 'Detailed flashcard list',
		'stylesheets' => array
		(
			'/css/2.0/widgets.css'
		)
	),
	
	'review' => array
	(
		'title'	      => 'Flashcard Review',
		'stylesheets' => array
		(
		),
		'javascripts' => array
		(
			'/js/lib/prototype.min.js'
		)
	),

	'summary' => array
	(
		'title'	      => 'Review Summary',
		
		'stylesheets' => array
		(
			'/css/2.0/widgets.css'
		),
		'javascripts' => array
		(
			'/js/lib/prototype.min.js',
			'/js/ui/uibase.min.js',
			'/js/ui/widgets.min.js'
		)
	),
	
	'fullscreen' => array
	(
		'title'	      => 'Flashcard Review',
		'stylesheets' => array
		(
		),
		'javascripts' => array
		(
			'/js/lib/prototype.min.js'
		)
	)
);
