<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the cards/{cardId}/stories REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 10 April, 2010
 * @package api
 * @subpackage cards
 **/

class cardsIdStoriesAction extends apiAction
{
  /**
   * Handles the cards/{cardId}/stories action and chooses the correct hanndler.
   *
   * @return coreView status
   **/
  public function execute($request)
  {
    $this->validateRequestAndSetUser();

    $cardId = $request->getParameter('cardId');

    // TODO: make sure this is a valid integer
    if (is_null($cardId)) {
      $e = new apiRestException('Invalid card specified.');
      $e->setStatusCode(400);
      throw $e;
    }

    switch($request->getMethod()) {
      case coreRequest::GET: {
        return $this->executeGetCardsIdStories($cardId);
        break;
      }
      case coreRequest::POST:
      case coreRequest::PUT:
      case coreRequest::DELETE: {
        $e = new apiRestException('POST, PUT and DELETE are currently not implemented for cards/{cardId}/stories.');
        $e->setStatusCode(405);
        throw $e;
      }
      default: break;
    }
  }

  /**
   * Handles the GET cards/{cardsId}
   */
  private function executeGetCardsIdStories($cardId) {

    $keyword = KanjisPeer::getKeyword($cardId);
    $stories = StoriesPeer::getPublicStories($cardId, $keyword, false, true);

    if (is_null($cardId)) {
      $e = new apiRestException('The card '.$cardId.' was not found.');
      $e->setStatusCode(404);
      throw $e;
    }

    if (is_null($stories) || (count($stories) == 0)) {
      // no stories
      $e = new apiRestException('No stories for card '.$cardId.'.');
      // 204 No Content
      $e->setStatusCode(204);
      throw $e;
    }

    return $this->renderText(coreJson::encode(apiRenderer::cardsIdStoriesGet($stories)));
  }
}

?>