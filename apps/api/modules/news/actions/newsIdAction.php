<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the news/{newsId} REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 30 March, 2010
 * @package api
 * @subpackage news
 **/

class newsIdAction extends apiAction
{
  /**
   * Handles the news/{newsId} action and chooses the correct handler.
   *
   * @return coreView status
   **/
  public function execute($request)
  {
    $newsId = $request->getParameter('newsId');

    if (!is_null($newsId)) {
      switch($request->getMethod()) {
        case coreRequest::GET: {
          return $this->executeGetNewsId($newsId);
          break;
        }
        case coreRequest::POST:
        case coreRequest::PUT:
        case coreRequest::DELETE: {
          $e = new apiRestException('POST, PUT and DELETE are currently not implemented for news/{newsId}.');
          $e->setStatusCode(501);
          throw $e;
        }
        default: break;
      }
    } else {
      $e = new apiRestException('Invalid news id provided.');
      $e->setStatusCode(404);
      throw $e;
    }
  }

  /**
   * Handles the GET news/{newsId}
   */
  private function executeGetNewsId($newsId) {
    $request = $this->getRequest();

    $post = SitenewsPeer::getPostByIdForRest($newsId);

    if (!$post) {
      $e = new apiRestException('News item not found.');
      $e->setStatusCode(404);
      throw $e;
    }

    return $this->renderText(coreJson::encode(apiRenderer::newsIdGet($post)));
  }
}

?>