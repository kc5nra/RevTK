<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Rest actions
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage rest
 **/

class restActions extends coreActions
{
	
	/**
	 * Handles a request for the apiKey.  This must be a GET with 
	 * HTTP Authentication headers passed in. 
	 * 
	 * @return the apiKey associated with the user
	 */
	public function executeApiKey($request) {
		
		if ($request->getMethod() != coreRequest::GET) {
			// if the request had no HTTP Auth headers, throw a 401 Unauthroized
			// TODO: 405 status codes MUST return an 'Allow:' header with a comma
			// separated list of allowed methods.
			$e = new apiRestException('Only the GET method is allowed for rest/apiKey');
			$e->setStatusCode(405);
			throw $e;
		}
		
		if (!isset($_SERVER['PHP_AUTH_USER']))
		{
			// if the request had no HTTP Auth headers, throw a 401 Unauthroized
			$e = new apiRestException('Not authentication headers found.');
			$e->setStatusCode(401);
			throw $e;
		}
			
		// check that user exists and password matches
		$user = UsersPeer::getUser($_SERVER['PHP_AUTH_USER']);
			
		// if the user couldn't be found or the password did not match, throw a 401 Unauthorized
		if (!$user || ($this->getUser()->getSaltyHashedPassword($_SERVER['PHP_AUTH_PW']) != $user['password']))
		{
			$e = new apiRestException('Authentication failed.');
			$e->setStatusCode(401);
			throw $e;
		}
		
		return $this->renderText(coreJson::encode(apiRenderer::restApiKeyGet($user['apikey'])));
	}
	
	/**
	 * Handles the Exception action (only called by apiRestException)
	 *
	 * @return coreView status
	 **/
	public function executeException($request) {
		$this->message = $request->getError('message');
		$this->statusCode = $this->getResponse()->getStatusCode();
		
		// This is ignored
		return coreView::ERROR;
	}
}

?>