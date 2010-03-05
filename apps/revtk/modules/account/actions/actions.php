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
 * @subpackage account
 * @author     Fabrice Denis
 */

class accountActions extends coreActions
{
	/**
	 * Create a new account.
	 * 
	 * @return 
	 */
	public function executeCreate($request)
	{
		if ($request->getMethod() != coreRequest::POST)
		{
			// setup form

			// development
			/*
			if (CORE_ENVIRONMENT==='dev')
			{
				$request->getParameterHolder()->add(array(
					'username' => '...' . rand(1,1000),
					'email' => '...',
					'password'=>'xxxxx',
					'password2'=>'xxxxx',
					'location'=>'Foo Bar')
				);
			}*/
		}
		else
		{
			$validator = new coreValidator($this->getActionName());
			
			if ($validator->validate($request->getParameterHolder()->getAll()))
			{
				$this->username = trim($request->getParameter('username'));
				$raw_password = trim($request->getParameter('password'));
				
				if (UsersPeer::usernameExists($this->username))
				{
					$request->setError('username_duplicate', 'Sorry, that username is already taken, please pick another one.');
					return coreView::SUCCESS;
				}

				$userinfo = array(
					'username' => trim($request->getParameter('username')),
					'password' => $this->getUser()->getSaltyHashedPassword($raw_password),
					'email'    => trim($request->getParameter('email')),
					'location' => trim($request->getParameter('location', ''))
				);
					
				// username is available, create user
				UsersPeer::createUser($userinfo);

				// create user on the PunBB forum with same username and password
				// NOT IN STAGING -- staging does not have a "test" forum database
				if (coreContext::getInstance()->getConfiguration()->getEnvironment() !== 'staging'
            && coreConfig::get('app_path_to_punbb') !== null)
				{
					try
					{
						$forumAccountSuccess = PunBBUsersPeer::createAccount($userinfo);
					}
					catch (coreException $e)
					{
						$forumAccountSuccess = false;
					}
	
					if (!$forumAccountSuccess)
					{
						$url = $this->getController()->genUrl('@contact');
						$request->setError('forum_account', 
							'Oops, there was a problem while accessing the forum database.<br/>'.
							'Please <a href="'.$url.'">contact the webmaster</a> with your username and password,<br/>'.
							'and we will create your forum account as soon as possible.' );
					}
				}
				else
				{
					// STAGING
					$forumAccountSuccess = false;
					$request->setError('forum_account', '(Forum account NOT created in test environment)');
				}

				// send email confirmation
				$mailer = new rtkMail();
				$mailer->sendNewAccountConfirmation($userinfo['email'], $userinfo['username'], $raw_password);
				
				return 'Done';
			}
		}
	}
	
	/**
	 * Edit Account
	 *
	 */
	public function executeEdit($request)
	{
		if ($request->getMethod() != coreRequest::POST)
		{
			// fill in form with current account details
			$userdata = $this->getUser()->getUserDetails();
			$formdata = array(
				'username' => $userdata['username'],
				'location' => $userdata['location'],
				'email'    => $userdata['email'],
				'timezone' => $userdata['timezone']
			);
			$request->getParameterHolder()->add($formdata);
		}
		else
		{
			$validator = new coreValidator($this->getActionName());
			
			if ($validator->validate($request->getParameterHolder()->getAll()))
			{
				$userdata = array(
					'email'    => trim($request->getParameter('email')),
					'location' => trim($request->getParameter('location', '')),
					'timezone' => (float) trim($request->getParameter('timezone'))
				);
				
				if (UsersPeer::updateUser($this->getUser()->getUserId(), $userdata))
				{
					$this->redirect('profile/index');
				}
			}
		}

	}

	/**
	 * Forgot Password page.
	 * 
	 * Request the email address, because the form is less easily abused this way
	 * (restting another person's password, or spamming another person's emails)
	 * 
	 * Still too simplistic, ideally should add another step so that the password
	 * is not automatically reset.
	 * 
	 */
	public function executeForgotPassword($request)
	{
		if ($request->getMethod() != coreRequest::POST)
		{
			return coreView::SUCCESS;
		}
		
		// handle the form submission
		$validator = new coreValidator($this->getActionName());
		
		if ($validator->validate($request->getParameterHolder()->getAll()))
		{
			$email_address = trim($request->getParameter('email_address'));
			$user = UsersPeer::getUserByEmail($email_address);

			if ($user)
			{
				// set new random password
				$raw_password = strtoupper(substr(md5(rand(100000, 999999)), 0, 8));

				// update the password on main site and forum
				$this->getUser()->changePassword($user['username'], $raw_password);
				
				// send email with new password, user username from db here to email user with the
				// username in the exact CaSe they registered with
				$mailer = new rtkMail();
				$mailer->sendForgotPasswordConfirmation($user['email'], $user['username'], $raw_password);

				return 'MailSent';
			}
			else
			{
				$request->setError('email_invalid', 'Sorry, no user found with that email address.');
 				return coreView::SUCCESS;
			}
		}
	}

	/**
	 * Change Password.
	 *
	 * Update the user's password on the RevTK site AND the corresponding PunBB forum account.
	 *	 
	 */
	public function executePassword($request)
	{
		if ($request->getMethod() != coreRequest::POST)
		{
			return coreView::SUCCESS;
		}
		
		// handle the form submission
		$validator = new coreValidator($this->getActionName());
		
		if ($validator->validate($request->getParameterHolder()->getAll()))
		{
			// verify old password
			$oldpassword = trim($request->getParameter('oldpassword'));
			
			$user = $this->getUser()->getUserDetails();
			if ($user && ($this->getUser()->getSaltyHashedPassword($oldpassword) == $user['password']) )
			{
				// proceed with password update
				
				$new_raw_password = trim($request->getParameter('newpassword'));
				
				$user = $this->getUser()->getUserDetails();

				// update the password on main site and forum
				$this->getUser()->changePassword($user['username'], $new_raw_password);

				// save username before signing out
				$this->username = $this->getUser()->getUserName();
	
				// log out user (sign out, clear cookie, clear punbb cookie(not on staging website))
				$this->getUser()->signOut();
				$this->getUser()->clearRememberMeCookie();
				if (coreContext::getInstance()->getConfiguration()->getEnvironment() !== 'staging'
            && coreConfig::get('app_path_to_punbb') !== null)
				{
					PunBBUsersPeer::signOut();
				}
				
				try
				{
					// send email confirmation
					$mailer = new rtkMail();
					$mailer->sendUpdatePasswordConfirmation($user['email'], $user['username'], $new_raw_password);
				}
				catch (coreException $e)
				{
					$request->setError('mail_error', 'Oops, we tried sending you a confirmation email but the mail server didn\'t respond. Your password has been updated though!');
				}

				return 'Done';
			}
			else
			{
				$request->setError('login_invalid', "Old password doesn't match.");
			}
		}

		// clear the password fields (avoid input mistakes)
		$request->setParameter('oldpassword', '');
		$request->setParameter('newpassword', '');
		$request->setParameter('newpassword2', '');
	}
}
