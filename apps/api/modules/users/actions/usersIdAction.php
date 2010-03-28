<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the users/{userId} REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage users
 **/

class usersIdAction extends apiAction
{
	/**
	 * Handles the users/{userId} action and chooses the correct hanndler.
	 *
	 * @return coreView status
	 **/
  public function execute($request)
  {
		$userId = $request->getParameter('userId');
		switch($request->getMethod()) {
			case coreRequest::GET: {
				return $this->executeGetUsersId($userId);
				break;
			}
			case coreRequest::POST:
			case coreRequest::PUT:
			case coreRequest::DELETE: {
				$e = new apiRestException('POST, PUT and DELETE are currently not implemented for users/{userId}.');
				$e->setStatusCode(501);
				throw $e;
			}
			default: break;
		}
	}

	/**
	 * Handles the GET user/{userId}
	 */
	private function executeGetUsersId($userId) {
		// TODO: make sure this is a valid integer
		if ($userId != null) {
			$user = UsersPeer::getUserById($userId);

			if ($user != null) {
				$this->user = $user;
				return $this->renderPartial('usersIdGet');
			} 
		}
		
		$e = new apiRestException('The user '.$userId.' was not found.');
		$e->setStatusCode(404);
		throw $e;	
	}
}

?>