<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Exception thrown by REST actions
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage lib
 **/

class apiRestException extends coreException
{
	private
		/**
		 * The status code to set in the response.
		 * @var integer
		 **/
		$statusCode = 500;

	/**
	 * Outputs the api REST exception XML.	Silently forwards to the unrouted module: rest, action: exception.
	 *	 This action handles the actual rendering of the error with a exceptionView.
	 *
	 * @return void
	 * @author John Bradley
	 **/
	public function printStackTrace()
	{
		$exception = is_null($this->wrappedException) ? $this : $this->wrappedException;
		$message	 = $exception->getMessage();

		$response = coreContext::getInstance()->getResponse();
		$response->setStatusCode($this->getStatusCode());

		// this sends a cookie unnecessarily
		$response->sendHttpHeaders(); 

		$context = coreContext::getInstance();
		
		// clean current output buffer
		while (@ob_end_clean());
		
		ob_start(coreConfig::get('sf_compressed') ? 'ob_gzhandler' : '');
		header('Content-Type: text/xml');
	
		// MAJOR HACK ALERT!!!!
		// This creates a internal route to a action/view
		// I'm sure there is a better way of doing this :(
	
		// add the error to request
		$request = $context->getRequest();
		$request->setError('message', $message);

		$controller = $context->getController();
		$moduleName = coreConfig::get('api_exception_module');
		$actionName = coreConfig::get('api_exception_action');

		// create an instance of the action
		$actionInstance = $controller->getAction($moduleName, $actionName);
		
		// execute the action with our request, response will always be successful
		$actionInstance->execute($request);
		
		// create a new view instance for rendering our REST xml error
		$viewInstance = new coreView(
			coreContext::getInstance(), 
			$moduleName,
			$actionName);
		
		// copy the variables
		$viewAttributes = $actionInstance->getVarHolder()->getAll();
		$viewInstance->getParameterHolder()->add($viewAttributes);
		
		echo $viewInstance->render();
		
		exit(1);
	}

	/**
	 * Gets the status code for a REST exception.
	 *
	 * @return status code
	 **/
	public function setStatusCode($value) {
		$this->statusCode = $value;
	}

	/**
	 * Sets the status code for a REST exception
	 *
	 * @return void
	 **/
	public function getStatusCode() {
		return $this->statusCode;
	}
	
}
