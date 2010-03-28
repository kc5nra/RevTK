<?php

/**
 * Reviewing the Kanji - API settings file.
 * 
 * All settings here become available through coreConfig.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package Config
 **/

return array
(
	/**
	 * TEST (local host, debug OFF, analytics tracking OFF)
	 */
	'test' => array
	(
		'no_script_name' => true
	),
	
	/**
	 * DEV (local host, debug ON, analytics tracking OFF)
	 */
	'dev' => array
	(
		'no_script_name' => false,

		'default_view_configuration' => array
		(
			'metas' => array
			(
				'robots' => 'NONE'
			)
		)		 
	),

	'all' => array
	(
		/**
		 * Controls the appearance of the front web controller in the url
		 * It is usually on for the production environment of your main application and off for the others.
		 */
		'no_script_name' => false,
	
		/**
		 * Helpers included in all templates by default
		 */
		'standard_helpers' => array(
			'Partial'
		),
	
		/**
		 * Database connection parameters.
		 */
		'database_connection' => array
		(
			'database'			 		=> 'local_database_name',
			'host'					 		=> 'localhost',
			'username'			 		=> 'root',
			'password'			 		=> '',
			'set_names_utf8' 		=> true
		),
	
		/**
		 * Specify where the libraries are to be loaded from
		 * (for performance reason, saves scanning through lib/ directories)
		 * 
		 * For each classname, the subdirectory from the coreConfig::get('root_dir') without trailing slash.
		 * The included filename will be the classname plus 'php' extension.
		 * 
		 * The class name can be a regular expression, which is very useful when
		 * using a naming convention, for example: '/^my[A-Z]/' => 'apps/myApp/myLibs'
		 */
		'autoload_classes'	=> array
		(
			'/^api[A-Z]/'			=> 'apps/api/lib'
		),
	
	
		/**
		 * Factories - here you can redefine (and extend) classes used by the framework.
		 * 
		 * Remember to add your custom class to the "autoload_classes" setting.
		 * 
		 * Configurable factories [default]:
		 *		 user		[coreBasicUserSecurity]
		 */
		'core_factories'	=> array(
			'action'				=> array('class' => 'apiAction'),
			'user'					=> array('class' => 'apiUser'),
		),
	
		/**
		 * Default Error 404 page.
		 */
		'api_exception_module' => 'rest',
		'api_exception_action' => 'exception',
		
		/**
		 * Default login and secure pages:
		 * 
		 * - If the user is not identified, he will be redirected to the default login action.
		 * - If the user is identified but doesn't have the proper credentials,
		 *	 he will be redirected to the default secure action ("credentials required")
		 */
		'login_module'	=> 'home',
		'login_action'	=> 'login',
		
		'secure_module' => 'default',
		'secure_action' => 'secure',
	
		/**
		 * Application-level view configuration.
		 * 
		 */
		'default_view_configuration' => array
		(
			'layout'							=> 'xml',
			'metas' => array
			(
				'content-type'		=> 'text/xml',
				'Content-Language'	=> 'en-us'
			)
		),
	
		/**
		 * Routes rules for the front controller, and the link helpers.
		 *
		 */
		'routing_config' => array
		(
			'routes' => array
			(
				'users'						=> array(
					'url'						=> '/users',
					'param'					=> array('module' => 'users', 'action' => 'users'),
				),
				
				'usersId'					=> array(
					'url'						=> '/users/:userId',
					'param'					=> array('module' => 'users', 'action' => 'usersId', 'userId' => 0)
				),
				
				'boxes'						=> array(
					'url'						=> '/boxes',
					'param'					=> array('module' => 'boxes', 'action' => 'boxes')
				),
				
				'default'					=> array(
					'url'						=> '/:module/:action/*'
				)
			)
		),
		
		// Server timezone to adjust MySQL time to the local time of the user
		'app_server_timezone'		 => 0,
	
		// from (email, name) for automatic mailings (registration, password change, ...)
		'app_email_robot'				 => array('email' => 'email-robot@localhost', 'name' => 'Reviewing the Kanji'),
		// to		(email, name) for contact page form
		'app_email_feedback_to'	 => array('email' => 'feedback@localhost', 'name' => 'DestName')
	)
);
