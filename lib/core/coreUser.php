<?php
/**
 * coreUser wraps a client session and provides accessor methods for user
 * attributes.
 * 
 * coreUser uses coreSessionStorage to store persistent data in the php
 * user session.
 * 
 * Old fix for the sporadic 'failed to initialize storage module' error:
 * ini_set("session.save_handler", "files");
 *
 * @package    Core
 * @subpackage User
 * @author     Fabrice Denis
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

class coreUser
{
	/**
	 * Name of the key in the $_SESSION array
	 */
	const ATTRIBUTE_NAMESPACE = 'core/user/coreUser/attributes';

	protected
		$options	 	 = array(),
		$attributeHolder = null,
 		$storage		 = null;

	/**
	 * Class constructor.
	 * 
	 * @note Currently bound to coreSessionStorage, move it to arguments if needed
	 *
	 * @see initialize()
	 */
	public function __construct($options = array())
	{
		// @todo: Symfony uses sfSessionStorage factory params (factories.yml)
		$storage = new coreSessionStorage( coreConfig::get('core_session_params', array()) );
		
		$this->initialize($storage, $options);

		if ($this->options['auto_shutdown'])
		{
			register_shutdown_function(array($this, 'shutdown'));
		}
	}
	
	/**
	 * Initializes this coreUser.
	 *
	 * Available options:
	 *
	 *	* auto_shutdown:   Whether to automatically save the changes to the session (true by default)
	 *
	 * @param coreStorage	A coreSessionStorage instance.
	 * @param array			An associative array of options.
	 *
	 * @return Boolean		true, if initialization completes successfully, otherwise false.
	 */
	public function initialize(coreSessionStorage $storage, $options = array())
	{
		$this->storage = $storage;

		// set defaults
		$this->options = array_merge(array(
			'auto_shutdown'	 => true
		), $options);

		$this->attributeHolder = new sfParameterHolder();

		// read attributes from storage
		$attributes = $storage->read(self::ATTRIBUTE_NAMESPACE);
		$this->attributeHolder->add($attributes);
	}

	public function getAttributeHolder()
	{
		return $this->attributeHolder;
	}

	public function getAttribute($name, $default = null)
	{
		return $this->attributeHolder->get($name, $default);
	}

	public function hasAttribute($name)
	{
		return $this->attributeHolder->has($name);
	}

	public function setAttribute($name, $value)
	{
		return $this->attributeHolder->set($name, $value);
	}


	/**
	 * Executes the shutdown procedure.
	 *
	 * @return void
	 */
	public function shutdown()
	{
		$attributes = $this->attributeHolder->getAll();

		// write attributes to the storage
		$this->storage->write(self::ATTRIBUTE_NAMESPACE, $attributes);

		session_write_close();
	}

}

/**
 * coreSessionStorage allows you to store persistent data in the user session.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>auto_start</b>   - [Yes]     - Should session_start() automatically be called?
 * # <b>session_name</b> - [core]    - The name of the session.
 *
 * @package    Core
 * @subpackage Storage
 * @author     Fabrice Denis
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */
class coreSessionStorage
{
  static protected
  	$sessionStarted = false;
    
	protected
		$options	 	= array();

	public function __construct($options = array())
	{
		$this->initialize($options);
		
		if ($this->options['auto_shutdown'])
		{
			register_shutdown_function(array($this, 'shutdown'));
		}
	}

	/**
	 * Available options:
	 *
	 *	* session_name:            The cookie name (symfony by default)
	 *	* session_id:              The session id (null by default)
	 *	* auto_start:              Whether to start the session (true by default)
	 *	* session_cookie_lifetime: Cookie lifetime
	 *	* session_cookie_path:     Cookie path
	 *	* session_cookie_domain:   Cookie domain
	 *	* session_cookie_secure:   Cookie secure
	 *	* session_cookie_httponly: Cookie http only (only for PHP >= 5.2)
	 *
	 * The default values for all 'session_cookie_*' options are those returned by the session_get_cookie_params() function
	 */
	public function initialize($options = null)
	{
		$cookieDefaults = session_get_cookie_params();

		$options = array_merge(array(
			'session_name' => 'core',
			'session_id'   => null,
			'auto_start'   => true,
			'session_cookie_lifetime' => $cookieDefaults['lifetime'],
			'session_cookie_path'     => $cookieDefaults['path'],
			'session_cookie_domain'   => $cookieDefaults['domain'],
			'session_cookie_secure'   => $cookieDefaults['secure'],
			'session_cookie_httponly' => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
		), $options);

//DBG::printr($options);exit;

		// initialize parent
		$this->options = array_merge(array('auto_shutdown' => true), $options);
		
		// set session name
		$sessionName = $this->options['session_name'];

		session_name($sessionName);

		if (!(boolean) ini_get('session.use_cookies') && $sessionId = $this->options['session_id'])
		{
			session_id($sessionId);
		}

		$lifetime = $this->options['session_cookie_lifetime'];
		$path	  = $this->options['session_cookie_path'];
		$domain	  = $this->options['session_cookie_domain'];
		$secure	  = $this->options['session_cookie_secure'];
		$httpOnly = $this->options['session_cookie_httponly'];
		if (version_compare(phpversion(), '5.2', '>='))
		{
			session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
		}
		else
		{
			session_set_cookie_params($lifetime, $path, $domain, $secure);
		}

		if ($this->options['auto_start'] && !self::$sessionStarted)
		{
			session_start();
			self::$sessionStarted = true;
		}
	}

	/**
	 * Reads data from this storage.
	 *
	 * The preferred format for a key is directory style so naming conflicts can be avoided.
	 *
	 * @param string A unique key identifying your data
	 *
	 * @return mixed Data associated with the key
	 */
	public function read($key)
	{
		$retval = null;

		if (isset($_SESSION[$key]))
		{
			$retval = $_SESSION[$key];
		}

		return $retval;
	}

	/**
	 * Removes data from this storage.
	 *
	 * The preferred format for a key is directory style so naming conflicts can be avoided.
	 *
	 * @param string A unique key identifying your data
	 *
	 * @return mixed Data associated with the key
	 */
	public function remove($key)
	{
		$retval = null;

		if (isset($_SESSION[$key]))
		{
			$retval = $_SESSION[$key];
			unset($_SESSION[$key]);
		}

		return $retval;
	}

	/**
	 * Writes data to this storage.
	 *
	 * The preferred format for a key is directory style so naming conflicts can be avoided.
	 *
	 * @param string A unique key identifying your data
	 * @param mixed	Data associated with your key
	 *
	 */
	public function write($key, $data)
	{
		$_SESSION[$key] = $data;
	}

	/**
	 * Executes the shutdown procedure.
	 *
	 */
	public function shutdown()
	{
		// don't need a shutdown procedure because read/write do it in real-time
	}
}
