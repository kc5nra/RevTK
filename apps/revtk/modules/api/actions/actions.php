<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 
 * 
 * @package    RevTK
 * @subpackage api
 * @author     John Bradley
 */

class apiActions extends coreActions
{
	
	public function executeTest() {
		if (!$this->getUser()->isAuthenticated()) {
			$this->getRequest()->setError('error_code', '1');
			$this->getRequest()->setError('error_message', 'not authenticated');
			return $this->forward('api', 'error');
		}
		
	}
	
	public function executeLogin()
	{
		if (isset($_SERVER['PHP_AUTH_USER']))
	    {

			// check that user exists and password matches
			$user = UsersPeer::getUser($_SERVER['PHP_AUTH_USER']);
			if (!$user || ($this->getUser()->getSaltyHashedPassword($_SERVER['PHP_AUTH_PW']) != $user['password']) )
			{
		    	$this->error_code = 1;
      			$this->error_message = 'login failed';
				return $this->forward('api', 'error');
			}

			// sign in user
			
			$this->getUser()->signIn($user);
			$this->session_id = session_id();
			$this->getResponse()->setContentType('text/xml');

  			return coreView::SUCCESS;
		} else {
			$this->getRequest()->setError('error_code', '1');
			$this->getRequest()->setError('error_message', 'login failed');
			return $this->forward('api', 'error');
		}
  		
	}
	
	public function executeLogout()
	{
		if ($this->getUser()->isAuthenticated()) {
			$this->getUser()->signOut();
		}
	}
	
	public function executeError() 
	{
		$this->error_code = $this->getRequest()->getError('error_code');
		$this->error_message = $this->getRequest()->getError('error_message');
	}
}

?>