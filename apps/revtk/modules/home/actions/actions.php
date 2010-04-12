<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Home module.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class homeActions extends coreActions
{
  /**
   * Home page.
   * 
   * @return 
   */
  public function executeIndex()
  {
    if ($this->getUser()->isAuthenticated())
    {                                                                                                                                                                                                                                         
//echo $this->getUser()->sqlLocalTime(); exit;
      
      // get member stats for quick summary
      $this->curFrameNum = ReviewsPeer::getHeisigProgressCount($this->getUser()->getUserId());
      $this->progress = rtkBook::getProgressSummary($this->curFrameNum);
      $this->countExpired = ReviewsPeer::getCountExpired($this->getUser()->getUserId());
      $this->countFailed = ReviewsPeer::getCountFailed($this->getUser()->getUserId());

      return 'Member';
    }
    
    // prepare unique homepage design
    
    return 'Guest';
  }

  /**
   * Sign In form.
   * 
   * @return 
   */
  public function executeLogin($request)
  {
    if ($request->getMethod() != coreRequest::POST)
    {
      // get the referer option from redirectToLogin()
      $referer = $this->getUser()->getAttribute('login_referer', '');

      // get other options from redirectToLogin()
      $username = $this->getUser()->getAttribute('login_username', '');

      // clear redirectToLogin() options
      $this->getUser()->getAttributeHolder()->remove('login_referer');
      $this->getUser()->getAttributeHolder()->remove('login_username');

      $this->getRequest()->setParameter('referer', empty($referer) ? '@homepage' : $referer);
      $this->getRequest()->setParameter('username', $username);

      // AUTO FILL FORM (DEVELOPMENT ONLY!)
      if (CORE_ENVIRONMENT === 'dev')
      {
        $request->getParameterHolder()->add(array(
          'username'=>'guest',
          'password'=>'',
          )
        );
      }
    }
    else
    {
      $validator = new coreValidator($this->getActionName());
      
      if ($validator->validate($request->getParameterHolder()->getAll()))
      {
        $username = trim($request->getParameter('username'));
        $raw_password = trim($request->getParameter('password'));
        $rememberme = $request->hasParameter('rememberme');

        // check that user exists and password matches
        $user = UsersPeer::getUser($username);
        if (!$user || ($this->getUser()->getSaltyHashedPassword($raw_password) != $user['password']) )
        {
          $request->setError('login_invalid', "Invalid username and/or password.");
          return;
        }

        // sign in user
        $this->getUser()->signIn($user);

        // optionally, create the remember me cookie
        if ($rememberme)
        {
          $this->getUser()->setRememberMeCookie($user['username'], $this->getUser()->getSaltyHashedPassword($raw_password));
        }

        // authenticate user on the community forums
        if (coreContext::getInstance()->getConfiguration()->getEnvironment() !== 'staging'
            && coreConfig::get('app_path_to_punbb') !== null)
        {
          PunBBUsersPeer::signIn($username, $raw_password, $rememberme);
        }
        
        // succesfully signed in
        return $this->redirect($this->getRequestParameter('referer', '@homepage'));
      }
    }
  }

  /**
   * Sign Out.
   * 
   * @return 
   */
  public function executeLogout($request)
  {
    $this->getUser()->signOut();
    
    // clear the rememberme cookie
    $this->getUser()->clearRememberMeCookie();
    
    // clear the PunBB cookie (not on the test website)
    if (coreContext::getInstance()->getConfiguration()->getEnvironment() !== 'staging'
        && coreConfig::get('app_path_to_punbb') !== null)
    {
      PunBBUsersPeer::signOut();
    }
    
    return $this->redirect('@homepage');
  }
  
  /**
   * Contact/Feedback Form page.
   * 
   */
  public function executeContact($request)
  {
    if ($request->getMethod() != coreRequest::POST)
    {
      return;
    }

    $validator = new coreValidator($this->getActionName());

    if ($validator->validate($request->getParameterHolder()->getAll()))
    {
      $name_from = trim($request->getParameter('name'));
      $reply_to  = trim($request->getParameter('email'));
      $message   = trim($request->getParameter('message'));

      try
      {
        $mailer = new rtkMail();
        $mailer->sendFeedbackMessage($reply_to, $name_from, $message);
      }
      catch(coreException $e)
      {
        $request->setError('smtp_mail', "I'm sorry, there was a problem sending the email. "
                                        ."Please try again shortly.");
        return;
      }

      return 'EmailSent';
    }
  }
  
  /**
   * Display the active members list.
   *
   */
  public function executeMemberslist($request)
  {
    ActiveMembersPeer::deleteInactiveMembers();
  }

  /**
   * Active members list table ajax update.
   * 
   * @return 
   */
  public function executeMemberslisttable($request)
  {
    return $this->renderComponent('home', 'MembersList');
  }
}
