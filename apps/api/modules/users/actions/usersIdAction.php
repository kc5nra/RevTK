<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
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
    $this->validateRequestAndSetUser();

    $userId = $request->getParameter('userId');

    // TODO: make sure this is a valid integer
    if (is_null($userId)) {
      $e = new apiRestException('Invalid user specified.');
      $e->setStatusCode(400);
      throw $e;
    }

    switch($request->getMethod()) {
      case coreRequest::GET: {
        return $this->executeGetUsersId($userId);
        break;
      }
      case coreRequest::POST:
      case coreRequest::PUT:
      case coreRequest::DELETE: {
        $e = new apiRestException('POST, PUT and DELETE are currently not implemented for users/{userId}.');
        $e->setStatusCode(405);
        throw $e;
      }
      default: break;
    }
  }

  /**
   * Handles the GET user/{userId}
   */
  private function executeGetUsersId($userId) {

    $user = UsersPeer::getUserById($userId);

    if (is_null($user)) {
      $e = new apiRestException('The user '.$userId.' was not found.');
      $e->setStatusCode(404);
      throw $e;
    }

    return $this->renderText(coreJson::encode(apiRenderer::usersIdGet($user)));
  }
}

?>