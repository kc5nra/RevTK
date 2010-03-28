<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the boxes REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage users
 **/

class boxesAction extends apiAction
{
	/**
	 * Handles the boxes action and chooses the correct hanndler.
	 *
	 * @return coreView status
	 **/
  public function execute($request)
  {
		$this->validateRequestAndSetUser();
		
		switch($request->getMethod()) {
			case coreRequest::GET: {
				return $this->executeGetBoxes();
				break;
			}
			case coreRequest::POST:
			case coreRequest::PUT:
			case coreRequest::DELETE: {
				$e = new apiRestException('POST, PUT and DELETE are currently not implemented for boxes.');
				$e->setStatusCode(501);
				throw $e;
			}
			default: break;
		}
	}

	/**
	 * Handles the GET boxes
	 */
	private function executeGetBoxes() {
	
		$boxes = ReviewsPeer::getLeitnerBoxCounts();
		
		$this->boxes = $boxes;
		return $this->renderPartial('boxesGet');
	}
}

?>