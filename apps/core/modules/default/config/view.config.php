<?php
/**
 * View configuration file for views of this module.
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

return array
(
	// index Action
	'index' => array
	(
		'title' 		   => 'Home | Core framework',
		'metas'            => array(
			'description'  => 'Description set through view config file for action "index"'
		)
	),
	
	'archives' => array
	(
		'title'	      	   => 'Archives',
		'metas'            => array(
			'robots'       => 'nofollow'
		)
	)
);
