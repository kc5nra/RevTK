<?php
/**
 * Sample security configuration file.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
	'fontpicker' => array
	(
		'is_secure'    => false
	),
	
	'all' => array
	(
		'is_secure'    => true,
		'credentials'  => array('member')
	)
);
