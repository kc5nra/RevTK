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