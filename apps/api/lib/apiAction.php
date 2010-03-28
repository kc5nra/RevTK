<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Adds authentication methods to the action.
 * Is there a better place I can put this? I can't seem 
 * to overload the Filter for authentication.
 * TODO: find a better way to do this
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage action
 **/

abstract class apiAction extends coreAction
{
	
	protected function validateRequestAndSetUser() {
		
		// check if the request had an api key in the header
		$apiKey = $this->getRequest()->getHttpHeader('revtk-api-key');
		
		if (is_null($apiKey)) {
			$e = new apiRestException('No api key was present in the request headers.');
			$e->setStatusCode(403);
			throw $e;
		}
		
		// get the user by api key
		$user = UsersPeer::getUserByApiKey($apiKey);
		
		if (is_null($user)) {
			$e = new apiRestException('Invalid api key was present in the request headers.');
			$e->setStatusCode(400);
			throw $e;
		}
		
		// TODO: actually validate based on the users credentials
		// current assumption is that everyone using this service is just a 'member'
		
		// 'sign in' this user 
		$this->getUser()->signIn($user);
		
		return true;
	}

}

?>