<?php
/**
 * coreUserBasicSecurity extends the user session with methods for
 * manipulating credentials and authentication of users.
 * 
 * Authentication state and credentials of the user are maintained
 * between pages through coreSessionStorage.
 *
 * @package    Core
 * @subpackage Security
 * @author     Fabrice Denis
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

class coreUserBasicSecurity extends coreUser
{
  const AUTH_NAMESPACE = 'core/user/coreUser/authenticated';
  const CREDENTIAL_NAMESPACE = 'core/user/coreUser/credentials';

  protected
    $credentials = null,
    $authenticated = null;

  /**
   *
   * @see coreUser
   */
  public function initialize(coreSessionStorage $storage, $options = array())
  {
    // initialize parent
    parent::initialize($storage, $options);

    // read data from storage
    $this->authenticated = $storage->read(self::AUTH_NAMESPACE);
    $this->credentials   = $storage->read(self::CREDENTIAL_NAMESPACE);

    if (is_null($this->authenticated))
    {
      $this->authenticated = false;
      $this->credentials   = array();
    }
  }

  public function shutdown()
  {
    $this->storage->write(self::AUTH_NAMESPACE, $this->authenticated);
    $this->storage->write(self::CREDENTIAL_NAMESPACE, $this->credentials);

    // call the parent shutdown method
    parent::shutdown();
  }

  /**
   * Clears all credentials.
   *
   */
  public function clearCredentials()
  {
    $this->credentials = null;
    $this->credentials = array();
  }

  /**
   * Returns an array containing the user's credentials
   *
   */
  public function listCredentials()
  {
    return $this->credentials;
  }

  /**
   * Removes a credential.
   *
   * @param  mixed credential
   */  
  public function removeCredential($credential)
  {
    if ($this->hasCredential($credential))
    {
      foreach ($this->credentials as $key => $value)
      {
        if ($credential == $value)
        {
          unset($this->credentials[$key]);
          return;
        }
      }
    }
  }  

  /**
   * Adds a credential.
   *
   * @param  mixed credential
   */
  public function addCredential($credential)
  {
    $this->addCredentials(func_get_args());
  }

  /**
   * Adds several credential at once.
   *
   * @param  mixed array or list of credentials
   */
  public function addCredentials()
  {
    if (func_num_args() == 0) return;

    // Add all credentials
    $credentials = (is_array(func_get_arg(0))) ? func_get_arg(0) : func_get_args();

    foreach ($credentials as $aCredential)
    {
      if (!in_array($aCredential, $this->credentials))
      {
        $this->credentials[] = $aCredential;
      }
    }
  }
  
  /**
   * Returns true if user has credential.
   *
   * @param  mixed credentials
   * @param  boolean useAnd specify the mode, either AND or OR
   * @return boolean
   *
   * @author Olivier Verdier <Olivier.Verdier@free.fr>
   */
  public function hasCredential($credentials, $useAnd = true)
  {
    if (!is_array($credentials))
    {
      return in_array($credentials, $this->credentials);
    }

    // now we assume that $credentials is an array
    $test = false;

    foreach ($credentials as $credential)
    {
      // recursively check the credential with a switched AND/OR mode
      $test = $this->hasCredential($credential, $useAnd ? false : true);

      if ($useAnd)
      {
        $test = $test ? false : true;
      }

      if ($test) // either passed one in OR mode or failed one in AND mode
      {
        break; // the matter is settled
      }
    }

    if ($useAnd) // in AND mode we succeed if $test is false
    {
      $test = $test ? false : true;
    }

    return $test;
  }

  /**
   * Returns true if user is authenticated.
   *
   * @return boolean
   */
  public function isAuthenticated()
  {
    return $this->authenticated;
  }

  /**
   * Sets authentication for user.
   *
   * @param  boolean
   */
  public function setAuthenticated($authenticated)
  {
    if ($authenticated === true)
    {
      $this->authenticated = true;
    }
    else
    {
      $this->authenticated = false;
      $this->clearCredentials();
    }
  }
}

/**
 * Check that the user is security by calling the getCredential() method of the action.
 * Once the credential has been acquired, verify the user has the same
 * credential by calling the hasCredential() method of SecurityUser.
 * 
 * Note: in Symfony this is a filter, here we use a simple class.
 * 
 * @package    Core
 * @subpackage Security
 * @author     Fabrice Denis
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */
class coreBasicSecurityFilter
{
  private
    $context        = null,
    $actionInstance = null;

  public function __construct($context, $actionInstance)
  {
    $this->context = $context;
    $this->actionInstance = $actionInstance;
  }

  /**
   * Execute the user security filter. 
   * 
   * If the security check fails, the user is forwarded to a login or secure(missing credentials) page.
   * 
   */
  public function execute()
  {
    // disable security on login and secure actions
    if (
      (coreConfig::get('login_module') == $this->context->getModuleName()) && (coreConfig::get('login_action') == $this->context->getActionName())
      ||
      (coreConfig::get('secure_module') == $this->context->getModuleName()) && (coreConfig::get('secure_action') == $this->context->getActionName())
    )
    {
      return;
    }
    if (!$this->context->getUser()->isAuthenticated())
    {
      // the user is not authenticated
      $this->forwardToLoginAction();
    }

    // the user is authenticated
    $credential = $this->actionInstance->getCredential();
    if (!is_null($credential) && !$this->context->getUser()->hasCredential($credential))
    {
      // the user doesn't have access
      $this->forwardToSecureAction();
    }

    // the user has access, continue
    return;
  }

  /**
   * Forwards the current request to the secure action (user doesn't have the proper credentials)
   *
   * @throws coreStopException
   */
  protected function forwardToSecureAction()
  {
    $this->context->getController()->forward(coreConfig::get('secure_module'), coreConfig::get('secure_action'));

    throw new coreStopException();
  }

  /**
   * Forwards the current request to the login action
   *
   * @throws coreStopException
   */
  protected function forwardToLoginAction()
  {
    $this->context->getController()->forward(coreConfig::get('login_module'), coreConfig::get('login_action'));

    throw new coreStopException();
  }
}
