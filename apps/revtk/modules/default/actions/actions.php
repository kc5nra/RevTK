<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This is the "default" module used by the framework to provide the default 404 page, etc.
 * 
 * Here you can customize the default 404 page, secure page, etc.
 * 
 * You can configure these to use a different module/action (see settings.php)
 * 
 * @author     Fabrice Denis
 * @package    Application
 * @subpackage Default
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

class defaultActions extends coreActions
{
	/**
	 * Congratulations page for creating an application
	 *
	 */
	public function executeIndex()
	{
	}
	
	/**
	 * Error page for page not found (404) error
	 *
	 */
	public function executeError404()
	{
	}
	
	/**
	 * Warning page for restricted area - requires login
	 *
	 */
	public function executeSecure()
	{
	}

	/**
	 * Redirects <website url>/admin to the backend app.
	 * 
	 * @see  Url routing @go_to_backend
	 */	
	public function executeGoToBackend()
	{
		$this->redirect(coreConfig::get('app_backend_url'));
	}
}
