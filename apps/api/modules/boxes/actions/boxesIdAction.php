<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the boxes/{boxId} REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage users
 **/

class boxesIdAction extends apiAction
{
	/**
	 * Handles the boxes/{boxId} action and chooses the correct hanndler.
	 *
	 * @return coreView status
	 **/
  public function execute($request)
  {
		$this->validateRequestAndSetUser();
	
		$boxId = $request->getParameter('boxId');
	
		if (!is_null($boxId)) {	
			switch($request->getMethod()) {
				case coreRequest::GET: {
					return $this->executeGetBoxesId($boxId);
					break;
				}
				case coreRequest::POST:
				case coreRequest::PUT:
				case coreRequest::DELETE: {
					$e = new apiRestException('POST, PUT and DELETE are currently not implemented for boxes/{boxId}.');
					$e->setStatusCode(501);
					throw $e;
				}
				default: break;
			}
		} else {
			$e = new apiRestException('Invalid box id provided.');
			$e->setStatusCode(404);
			throw $e;
		}
	}

	/**
	 * Handles the GET boxes/{boxId}
	 */
	private function executeGetBoxesId($boxId) {
		// TODO: make sure this is a valid integer
		$request = $this->getRequest();
		
		$reviewBox  = $boxId;
    $reviewType = $request->getParameter('type', 'expired');
    $reviewFilt = $request->getParameter('filt', '');
    $reviewMerge= $request->getParameter('merge') ? true : false;
		
		$flashcards = ReviewsPeer::getFlashcards($reviewBox, $reviewType, $reviewFilt, $reviewMerge);
		
		$flashcardData = null;
		if (!is_null($flashcards) && count($flashcards) > 0) {
			foreach($flashcards as $card) {
				// TODO: validate that $card is an integer (or can be cast as one)
				$data = KanjisPeer::getFlashcardRestData($card);
				
				if (!is_null($data)) {
					$flashcardData[$card] = $data;
				} else {
					// card stored in a box failed the lookup (invalid card id, missing, db error, etc.)
					$e = new apiRestException('Unable to resolve card '.$card.' in box '.$reviewBox.'.');
					$e->setStatusCode(500);
					throw $e;
				}
			}
		} else {
			// empty box
			$e = new apiRestException('No cards in box '.$reviewBox.'.');
			// 204 No Content
			// when 204 is set, even though there is a message, most clients will ignore any returned payload.
			// TODO create a new exception type that handles 'non-error' cases (200s, 300s)
			$e->setStatusCode(204);
			throw $e;
		}
		
		$this->flashcardData = $flashcardData;
		
		return $this->renderPartial('boxesIdGet');			
	}
}

?>