<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtkUser adds utility methods to the Core user class.
 * 
 * Methods
 *   getUserId()
 *   getUserName()
 *   getUserTimeZone()
 *   getUserDetails()     UsersPeer record
 *   getLocalPrefs()      LocalPrefs instance
 *   
 *   redirectToLogin($options = array())      Redirect unauthenticated user to login page
 *   sqlLocalTime($localTimezone = null)      Returns SQL statement for user's local date+time
 * 
 * @author     Fabrice Denis
 */

class rtkUser extends coreUserBasicSecurity
{
  protected
    $localPrefs = null;
  
  /**
   * The "Remember me" cookie name and lifetime in seconds.
   */
  const COOKIE_NAME   = 'RevTK';
  const COOKIE_EXPIRE = 1296000; // 60*60*24*15

  /**
   * 
   */
  public function initialize(coreSessionStorage $storage, $options = array())
  {
    parent::initialize($storage, $options);

    // sign in unauthenticated user if a "remember me" cookie exists
    if (!$this->isAuthenticated())
    {
      if ($cookieData = coreContext::getInstance()->getRequest()->getCookie(self::COOKIE_NAME))
      {
        $value = unserialize(base64_decode($cookieData));
        $username = $value[0];
        $saltyPassword = $value[1];
        
        // sign in user if user is valid and password from cookie matches the one in database
        $user = UsersPeer::getUser($username);
        if ($user && ($saltyPassword == $user['password']) )
        {
          $this->signIn($user);
        }
      }
    }
    
    // session duration preferences
    $this->localPrefs = new LocalPrefs($this);
  }

  /**
   * Getter method for user session attribute.
   * 
   */
  public function getUserName()
  {
    return $this->getAttribute('username', '');
  }

  /**
   * Getter method for user session attribute.
   * 
   */
  public function getUserId()
  {
    return $this->getAttribute('userid', null);
  }

  /**
   * Getter method for user session attribute.
   * 
   */
  public function getUserTimeZone()
  {
    return $this->getAttribute('usertimezone', null);
  }

  /**
   * Return UsersPeer row data for authenticated user.
   * 
   */
  public function getUserDetails()
  {
    return UsersPeer::getUserById($this->getUserId());
  }

  /**
   * Return the LocalPrefs instance.
   * 
   * @return 
   */
  public function getLocalPrefs()
  {
    return $this->localPrefs;
  }

  /**
   * Sign In the user.
   * 
   * Also sign in user on the PunBB forum, by way of the forum cookie.
   * The "remember me" option applies similarly to the forum cookie.
   * 
   * @param  array  $user  UsersPeer row
   * @return 
   */  
  public function signIn($user)
  {
    $this->setAttribute('userid', $user['userid']);
    $this->setAttribute('username', $user['username']);
    $this->setAttribute('usertimezone', $user['timezone']);

    $this->clearCredentials();
    $this->addCredential('member');
    switch($user['userlevel'])
    {
      case UsersPeer::USERLEVEL_ADMIN:
        $this->addCredential('admin');
        break;
      default:
        break;
    }

    // authenticate the user
    $this->setAuthenticated(true);

    // update user's last login timestamp
    UsersPeer::setLastlogin($user['userid']);
  }
  
  /**
   * Sign Out the user, sets user as "Guest"
   * 
   * @return 
   */
  public function signOut()
  {
    $this->getAttributeHolder()->clear();
    $this->clearCredentials();
    $this->setAuthenticated(false);
  }
  
  /**
   * Sets the persistent session cookie.
   * 
   */
  public function setRememberMeCookie($username, $saltyPassword)
  {
    $value = base64_encode( serialize(array($username, $saltyPassword)) );
    coreContext::getInstance()->getResponse()->setCookie(self::COOKIE_NAME, $value, time()+self::COOKIE_EXPIRE, '/');
  }

  /**
   * Clears the persistent session cookie.
   * 
   * @return 
   */
  public function clearRememberMeCookie()
  {
    coreContext::getInstance()->getResponse()->setCookie(self::COOKIE_NAME, '', time() - 3600, '/');
  }
  
  /**
   * Update the user password in the main site and forum databases.
   * 
   * @param object $user
   * @param object $raw_password
   */
  public function changePassword($username, $raw_password)
  {
    // hash password for database
    $hashedPassword = $this->getSaltyHashedPassword($raw_password);

    // set new user password
    $user_id = UsersPeer::getUserId($username);
    UsersPeer::setPassword($user_id, $hashedPassword);
    
    // set new password on forum account (not in staging)
    if (coreContext::getInstance()->getConfiguration()->getEnvironment() !== 'staging')
    {
      // only with linked PunBB forum
      if (coreConfig::get('app_path_to_punbb') !== null)
      {
        PunBBUsersPeer::setPassword($username, $raw_password);
      }
    }
  }

  /**
   * Returns hashed password.
   * 
   * We use sha1() like PunBB to store passwords.
   * 
   * Ideally could store a random salt with each user, eg:
   * 
   *   salt VARCHAR(32)      =>  md5(rand(100000, 999999).$this->getNickname().$this->getEmail());
   *   password VARCHAR(40)  =>  sha1($salt.$raw_password)
   * 
   * @param string  $password  Non-encrypted password.
   */
  public function getSaltyHashedPassword($raw_password)
  {
    return sha1($raw_password);
  }
  
  /**
   * Redirect unauthenticated user to login action.
   * 
   * Options:
   * 
   *   username => Fill in the user name of the login form
   *   referer  => Page to return the user to after signing in
   * 
   * @param array $params  Options to pass to the login page
   */
  public function redirectToLogin($options = array())
  {
    if (isset($options['referer']))
    {
      $this->setAttribute('login_referer', $options['referer']);
    }
    
    if (isset($options['username']))
    {
      $this->setAttribute('login_username', $options['username']);
    }

    $login_url = coreConfig::get('login_module') . '/' . coreConfig::get('login_action');
    coreContext::getInstance()->getActionInstance()->redirect($login_url);
  }
  
  /**
   * Returns a SQL statement which returns a date+time adjusted to the
   * timezone of the user. The date returned by
   * this statement will switch at midnight time of the user's timezone
   * (assuming the user set the timezone properly).
   * (the user's timezone range is -12...+14)
   * 
   * @return String   MySQL ADDDATE() expression that evaluates to the user's localized time
   */
  public function sqlLocalTime()
  {
    $localTimezone = $this->getUserTimeZone();
    $timediff = $localTimezone - coreConfig::get('app_server_timezone');
    $hours = floor($timediff);
    $minutes = ($hours != $timediff) ? '30' : '0';  // some timezones have half-hour precision, convert to minutes

    $sqlDate = 'ADDDATE(NOW(), INTERVAL \''.$hours.':'.$minutes.'\' HOUR_MINUTE)';
    return $sqlDate;
  }

}
