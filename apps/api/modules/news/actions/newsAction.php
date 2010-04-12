<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the news REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage news
 **/

class newsAction extends apiAction
{
  /**
   * Handles the news action and chooses the correct handler.
   *
   * @return coreView status
   **/
  public function execute($request)
  {

    switch($request->getMethod()) {
      case coreRequest::GET: {
        return $this->executeNews($request);
        break;
      }
      case coreRequest::POST:
      case coreRequest::PUT:
      case coreRequest::DELETE: {
        $e = new apiRestException('POST, PUT and DELETE are currently not implemented for news.');
        $e->setStatusCode(501);
        throw $e;
      }
      default: break;
    }
  }

  /**
   * Handles the GET boxes
   */
  private function executeNews($request) {

    // retrieve an array with the latest 5 news entries
    // TODO: Add the code for allowing how many entries to get, with some sane max
    // SitenewsPeer::getMostRecentPosts($maxPosts, $isRestFormatted)

    $latestNewsEntries = SitenewsPeer::getMostRecentPosts(5, true  );

    return $this->renderText(coreJson::encode(apiRenderer::newsGet($latestNewsEntries)));
  }
}

?>