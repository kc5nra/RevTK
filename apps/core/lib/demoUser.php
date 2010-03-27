<?php
/**
 * This is a demonstration of extending the Core framework classes.
 * 
 * See "factories" in settings.php.
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

class demoUser extends coreUserBasicSecurity
{
  public function initialize(coreSessionStorage $storage, $options = array())
  {
    parent::initialize($storage, $options);
  }

  /**
   * Demonstrates having extended the core user class.
   * 
   * This method is used by the Security demo.
   * 
   * @link /test/security/login
   * 
   * @param string $signInName   Sign in name (just a nickname for the demo)
   * @param array  $credentials[optional]  Credentials as an array
   */  
  public function signInDemo($signInName, $credentials = array())
  {
    $this->setAttribute('name', $signInName);
    $this->clearCredentials();
    $this->addCredentials($credentials);
    $this->setAuthenticated(true);
  }
  
  /**
   * Sign Out the user.
   * 
   * @return 
   */
  public function signOutDemo()
  {
    $this->clearCredentials();
    $this->setAuthenticated(false);
  }
}
