<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Send RevTK mails.
 * 
 * Requires configuration in settings.php:
 * 
 *   Each configuration value is an associative array with 'email' and 'name' properties.
 * 
 *  // from address for automatic mailings (registration, password change, ...)
 *  'app_email_robot'        => array('email' => 'admin@kanji.koohii.com', 'name' => 'Reviewing the Kanji'),
 *  
 *  // to address for contact form
 *  'app_email_feedback_to'  => array('email' => 'beta@kanji.koohii.com', 'name' => 'Fabrice')
 *    
 */

class rtkMail extends MailAbstract
{
	/**
	 * Sends Forgot Password email with new password.
	 * 
	 */
	public function sendForgotPasswordConfirmation($userAddress, $userName, $rawPassword)
	{
		$from = coreConfig::get('app_email_robot');
		$this->setFrom($from['email'], isset($from['name']) ? $from['name'] : '');

		$this->addTo($userAddress, $userName);
		$this->setSubject('Your new password at Reviewing the Kanji');
		$this->setPriority(1);
		$body = $this->renderTemplate('forgotPasswordConfirmation', array('username' => $userName, 'password' => $rawPassword));
		$this->setBodyText($body);
		$this->send();
	}

	/**
	 * Sends email to new members to confirm account details.
	 * 
	 */
	public function sendNewAccountConfirmation($userAddress, $userName, $rawPassword)
	{
		$from = coreConfig::get('app_email_robot');
		$this->setFrom($from['email'], isset($from['name']) ? $from['name'] : '');

		$this->addTo($userAddress, $userName);
		$this->setSubject('Welcome to Reviewing the Kanji');
		$this->setPriority(1);

		$forum_uid = coreConfig::get('app_path_to_punbb') !== null ? PunBBUsersPeer::getForumUID($userName) : false;

		$body = $this->renderTemplate('newAccountConfirmation',	array(
			'username' => $userName, 'password' => $rawPassword, 'email' => $userAddress, 'forum_uid' => $forum_uid));
		$this->setBodyText($body);
		$this->send();
	}
	
	/**
	 * Send a feedback email to the webmaster.
	 * 
	 * @param string $name_from   Author name
	 * @param string $email_from  Reply to address, can be empty
	 * @param string $message     The message
	 */
	public function sendFeedbackMessage($authorAddress, $authorName, $message)
	{
		$message = trim(strip_tags($message));

		$this->setFrom($authorAddress, $authorName);

		$to = coreConfig::get('app_email_feedback_to');
		$this->addTo($to['email'], isset($to['name']) ? $to['name'] : '');

		$this->setSubject('Feedback from '.$authorName);
		$this->setBodyText($message);
		$this->send();
	}

	/**
	 * Sends email to confirm the new login details after a password update.
	 * 
	 */
	public function sendUpdatePasswordConfirmation($userAddress, $userName, $rawPassword)
	{
		$from = coreConfig::get('app_email_robot');
		$this->setFrom($from['email'], isset($from['name']) ? $from['name'] : '');

		$this->addTo($userAddress, $userName);
		$this->setSubject('Account update at Reviewing the Kanji');

		$body = $this->renderTemplate('updatedPasswordConfirmation', array(
			'username' => $userName, 'password' => $rawPassword, 'email' => $userAddress));
		$this->setBodyText($body);
		$this->send();
	}
}
