<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Exception thrown by REST actions
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 * @subpackage lib
 **/

class apiRestException extends coreException
{
	private
		/**
		 * The status code to set in the response.
		 * @var integer
		 **/
		$statusCode = 500;

	/**
	 * Outputs the api REST exception XML.	Silently forwards to the unrouted module: rest, action: exception.
	 *	 This action handles the actual rendering of the error with a exceptionView.
	 *
	 * @return void
	 **/
	public function printStackTrace()
	{
		$exception = is_null($this->wrappedException) ? $this : $this->wrappedException;
		$message	 = $exception->getMessage();

		$response = coreContext::getInstance()->getResponse();
		$response->setStatusCode($this->getStatusCode());

		// this sends a cookie unnecessarily
		$response->sendHttpHeaders(); 

		$context = coreContext::getInstance();
		
		// clean current output buffer
		while (@ob_end_clean());
		
		ob_start(coreConfig::get('sf_compressed') ? 'ob_gzhandler' : '');
		header('Content-Type: application/json');
	
		echo coreJson::encode(apiRenderer::restException($message, $this->getStatusCode()));
		
		exit(1);
	}

	/**
	 * Gets the status code for a REST exception.
	 *
	 * @return status code
	 **/
	public function setStatusCode($value) {
		$this->statusCode = $value;
	}

	/**
	 * Sets the status code for a REST exception
	 *
	 * @return void
	 **/
	public function getStatusCode() {
		return $this->statusCode;
	}
	
}
