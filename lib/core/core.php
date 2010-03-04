<?php
/**
 * Core : bootstrap file for the Core framework.
 * 
 * @author     Fabrice Denis
 * @package    Core
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

/**
 * coreProjectConfiguration handles project configuration
 * 
 * @package Core
 */
class coreProjectConfiguration
{
	protected
		$context     = null,
  	$rootDir     = null,
		$coreLibDir  = null;

	/**
	 * Initializes and setup configuration for the application
	 * 
	 * @param string  $rootDir       Project root directory
	 * @param string  $application   Application name
	 * @param boolean $debug         True for debug mode, false for production
	 */
	public function __construct($rootDir = null, $application, $debug)
	{
    $this->rootDir = is_null($rootDir) ? self::guessRootDir() : realpath($rootDir);
   	$this->coreLibDir = realpath(dirname(__FILE__));

		// class autoloading
		coreAutoload::register();

    ini_set('magic_quotes_runtime', 'off');
    ini_set('register_globals', 'off');

		$this->setRootDir($this->rootDir);

		// setup application directories
	  coreConfig::set('debug', (boolean) $debug);
		$this->setAppDir(coreConfig::get('apps_dir').DIRECTORY_SEPARATOR.$application);

		// set default constants required by Symfony libraries
		$this->setSymfonyConfig($debug);

		require_once($this->coreLibDir.'/coreError.php');
		coreError::initialize($debug);
		
		// handy functions for debugging output and raising user errors
		require_once($this->coreLibDir.'/coreDebug.php');
	}

	/**
	 * Returns root dir assuming core.php is in :root:/lib/core/
	 * 
	 * @return string Absolute path to root dir
	 */
	static public function guessRootDir()
	{
		return realpath(dirname(__FILE__).'/../..');
	}

	/**
	 * Sets project directories.
	 *
	 * @param string The project root directory
	 */
	public function setRootDir($rootDir)
	{
		$this->rootDir = $rootDir;
		
		coreConfig::add(array(
		  'root_dir'    => $rootDir,
		  'core_dir'    => $this->coreLibDir,

		  // global directory structure
		  'apps_dir'    => $rootDir.DIRECTORY_SEPARATOR.'apps',
		  'lib_dir'     => $rootDir.DIRECTORY_SEPARATOR.'lib',
		  'config_dir'  => $rootDir.DIRECTORY_SEPARATOR.'config'
		));

		$this->setWebDir($rootDir.DIRECTORY_SEPARATOR.'web');
		$this->setCacheDir($rootDir.DIRECTORY_SEPARATOR.'cache');
	}

	/**
	 * Sets the app directories.
	 *
	 * @param string The absolute path to the app dir.
	 */
	public function setAppDir($appDir)
	{
		coreConfig::add(array(
			'app_dir' => $appDir,
	
			// application directory structure
			'app_config_dir'   => $appDir.DIRECTORY_SEPARATOR.'config',
			'app_lib_dir'      => $appDir.DIRECTORY_SEPARATOR.'lib',
			'app_module_dir'   => $appDir.DIRECTORY_SEPARATOR.'modules',
			'app_template_dir' => $appDir.DIRECTORY_SEPARATOR.'templates'
		));
	}
	

	/**
	 * Sets the cache root directory.
	 *
	 * @param string The absolute path to the cache dir.
	 */
	public function setCacheDir($cacheDir)
	{
		coreConfig::set('sf_cache_dir', $cacheDir);
	}
	
	/**
	 * Sets the web root directory.
	 *
	 * @param string The absolute path to the web dir.
	 */
	public function setWebDir($webDir)
	{
		coreConfig::add(array(
		  'sf_web_dir'    => $webDir,
		  'sf_upload_dir' => $webDir.DIRECTORY_SEPARATOR.'uploads',
		));
	}

	/**
	 * Gets directories where controller classes are stored for a given module.
	 *
	 * Note: we don't use a default module stored in the framework paths,
	 *  like Symfony, instead we provide the default "frontend" application,
	 *  with a "default" module and preconfigured error404 page, etc.
	 *
	 * @param string The module name
	 * @return array An array of directories
	 */
	public function getControllerDirs($moduleName)
	{
		$dirs = array();

		// look first for application modules
		$dirs[] = coreConfig::get('app_module_dir').'/'.$moduleName.'/actions';

		return $dirs;
	}

	/**
	 * Set some symfony settings for symfony classes that we use.
	 * 
	 * Note! In the Symfony includes, the sfConfig calls are replaced by coreConfig calls.
	 */
	private function setSymfonyConfig($debug)
	{
		coreConfig::set('sf_root_dir', $this->rootDir);
		coreConfig::set('sf_symfony_lib_dir', coreConfig::get('core_dir').DIRECTORY_SEPARATOR.'sf');

		// content encoding for html
		coreConfig::set('sf_charset', 'utf-8');

		// testing or production environment (remote)
		coreConfig::set('sf_test',  $debug);
		
		// debugging
		coreConfig::set('sf_debug', $debug);

		// output compression for html and also javascripts
		coreConfig::set('sf_compressed', true && coreToolkit::detectGzipEncodingSupport());
		
	}
}

/**
 * coreApplicationConfiguration allows to configure a Core framework application.
 * 
 * The configuration file should be stored as /apps/<myapp>/<myapp>Configuration.php
 * 
 */
abstract class coreApplicationConfiguration extends coreProjectConfiguration
{
  	protected
		$application = null,
		$environment = null,
		$debug       = false,
		$config      = array();

	/**
	 * Constructor.
	 *
	 * @param string            $environment    The environment name
	 * @param Boolean           $debug          true to enable debug mode
	 * @param string            $rootDir        The project root directory
	 */
	public function __construct($environment, $debug, $rootDir = null)
	{
		$this->environment = $environment;
		$this->debug       = (boolean) $debug;
		$this->application = str_replace('Configuration', '', get_class($this));
		
		// initialize project configuration
		parent::__construct($rootDir = null, $this->application, $this->debug);
		
		// application settings are added now, so they can redefine any of the defaults!
		$app_settings = $this->getApplicationSettings();
		coreConfig::add($app_settings);

		// initialize application configuration
		$this->configure();
	}
	
	/**
	 * Configures the project configuration.
	 *
	 * Override this method if you want to customize your application configuration.
	 */
	abstract public function configure();

	/**
	 * Returns the application name.
	 *
	 * @return string The application name
	 */
	public function getApplication()
	{
		return $this->application;
	}

	/**
	 * Returns the environment name.
	 *
	 * @return string The environment name
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}


	/**
	 * Return environment-specific application settings.
	 * 
	 * @return array
	 */
	public function getApplicationSettings()
	{
		$settings = require(coreConfig::get('app_config_dir').'/settings.php');
		
		$appSettings = isset($settings['all']) ? $settings['all'] : array();

		$envName = $this->getEnvironment();
		$envSettings = isset($settings[$envName]) ? $settings[$envName] : null;

		if ($envSettings!==null)
		{
			$appSettings = coreToolkit::arrayDeepMerge($appSettings, $envSettings);
		}

	//	DBG::printr($appSettings);exit;
		return $appSettings;
	}
}


/**
 * Handle autoloading of framework and application classes and models.
 * 
 * @package Core
 */
class coreAutoload
{
  	static protected $instance = null;

	protected function __construct()
	{
	}
	
	/**
	 * Retrieves the singleton instance of this class.
	 * 
	 * @return coreAutoload  Instance.
	*/
	static public function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new coreAutoload();
		}
		return self::$instance;
	}
	
	/**
	 * Register sfCoreAutoload in spl autoloader.
	 * 
	 * @return void
	 */
	static public function register()
	{
		if (!spl_autoload_register(array(self::getInstance(), 'autoload')))
		{
			throw new Exception(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
		}
	}

	/**
	 * Unregister sfCoreAutoload from spl autoloader.
	 * 
	 * @return void
	 */
	static public function unregister()
	{
		spl_autoload_unregister(array(self::getInstance(), 'autoload'));
	}

	/**
	 * Autoloading of classes/models
	 * 
	 * Autoload attempts to load the file in this order :
	 * 
	 * App:$classes[$class] =>  if class is defined there, use the specified path.
	 * core<xyz>            =>  load a core class from core/<xyz>.php
	 * <xyz>Peer            =>  load a database table model from lib/model/<xyz>.php
	 * ...otherwise...      =>  try to load unspecified data model from lib/model/
	 * 
	 * @param  string  A class name.
	 * @return boolean Returns true if the class has been loaded
	 * @link   http://www.php.net/autoload
	 */
	public function autoload($class)
	{
		$matches = array();

		// load Core framework classes and Symfony Exceptions
		// if (strpos($class, 'core')===0 || strpos($class, 'Exception'))
		if (strpos($class, 'core')===0 || preg_match('/^(core|sf)\w+Exception/', $class))
		{
			if (isset($this->classes[$class])) {
				$class = $this->classes[$class];
			}
			require_once(dirname(__FILE__).'/'.$class.'.php');
			return true;
		}

		// load imported Symfony classes
		if (preg_match('/^sf[A-Z]/', $class))
		{
			require_once(dirname(__FILE__).'/lib/sf/'.$class.'.class.php');
			return true;
		}

		// load data model classes
		if (preg_match('/^(\\w+)Peer$/', $class, $matches))
		{
			$dirs = array();
			$dirs[] = coreConfig::get('app_lib_dir').'/model';
			$dirs[] = coreConfig::get('lib_dir').'/model';

			$fileName = $class.'.php';
			foreach ($dirs as $dir)
			{
				if (is_readable($dir.'/'.$fileName))
				{
					require_once($dir.'/'.$fileName);

					// verify naming of the class
					if (!class_exists($class, false))
					{
						echo "<i><b>$class</b></i> class file found but class name does not match.";
						exit;
					}

					call_user_func(array($class, 'getInstance'));

					return true;
				}
			}
		}

		// load application classes configured in settings.php
		$app_classes = coreConfig::get('autoload_classes', array());
		$app_class_path = null;
		if (isset($app_classes[$class]))
		{
			$app_class_path = $app_classes[$class];
		}
		else
		{
			// check if class name matches one of the regexps, the regexp should start and end
			// with the / character, the path set with the first match is used
			foreach ($app_classes as $pattern => $path)
			{
				if (substr($pattern, 0, 1)=='/')
				{
					if (preg_match($pattern, $class))
					{
						$app_class_path = $path;
						break;
					}
				}
			}
		}
		if ($app_class_path !== null)
		{
			require_once(coreConfig::get('root_dir').'/'.$app_class_path.'/'.$class.'.php');
			
			return true;
		}
		
		// look in the base /lib directory
		$path = coreConfig::get('lib_dir').'/'.$class.'.php';
		if (is_readable($path))
		{
			require_once($path);
		}
	}

	// Where to load core classes from.
	// Helps with subdirectories but also when multiple classes are kept in one file
	protected $classes = array
	(
		'coreActions' => 'coreAction',
		'coreBasicSecurityFilter' => 'coreUserBasicSecurity',
		'coreWebRequest' => 'coreRequest',
		'coreWebController' => 'coreController',
		// Exceptions
		'coreException' => 'exception/coreException',
		'coreStopException' => 'exception/coreException',
		'coreControllerException' => 'exception/coreException',
		'coreDatabaseException' => 'exception/coreException',
		'coreError404Exception' => 'exception/coreError404Exception',
		// Symfony Exceptions
		'sfException' => 'exception/coreException',
		'sfConfigurationException' => 'exception/coreException',
		'sfError404Exception' => 'exception/coreError404Exception'
	);
}


/**
 * The context object provides information about the current application context,
 * references to the main Core objects (request, user, database, ...).
 * 
 * It is available at the beginning of the running action.
 *  
 */
class coreContext
{
	protected static $instance = null;

	protected
		$configuration   = null,
		$factories       = array(),
		$factoriesConfig = array
		(
			'user'     => array('class' => 'coreUserBasicSecurity'),
			'request'  => array('class' => 'coreWebRequest'),
			'response' => array('class' => 'coreWebResponse')
		);

	public function __construct(coreProjectConfiguration $configuration)
	{
		$this->configuration = $configuration;
		
		// configure the core factories
		$this->factoriesConfig = array_merge($this->factoriesConfig, coreConfig::get('core_factories', array()));
	}

	/**
	 * Create an instance for the current action.
	 * 
	 * @param  object       AbstractController implementation.
	 * @return coreContext  coreContext instance
	 */
	static public function createInstance(coreProjectConfiguration $configuration)
	{
		$class = __CLASS__;
		self::$instance = new $class($configuration);
	}

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return coreContext A coreContext implementation instance.
	 */
	static public function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::createInstance();
		}

		return self::$instance;
	}

	/**
	 * Checks to see if there has been a context created
	 *
	 * @return boolean	True is instanced, otherwise false
	 */
	public static function hasInstance()
	{
		return isset(self::$instance);
	}

	/**
	 * Create an instance of a Core framework class
	 * 
	 * @return mixed  Class instance
	 */
	public function loadFactory($name)
	{
		if (!isset($this->factories[$name]))
		{
			$class = $this->factoriesConfig[$name]['class'];

			$this->set($name, new $class());
		}
		return $this->factories[$name];
	}

	/**
	 * Gets an object from the current context.
	 *
	 * @param	string The name of the object to retrieve
	 *
	 * @return object The object associated with the given name
	 */
	public function get($name)
	{
		if (!$this->has($name))
		{
			throw new coreException(sprintf('The "%s" object does not exist in the current context.', $name));
		}

		return $this->factories[$name];
	}

	/**
	 * Puts an object in the current context.
	 *
	 * @param string The name of the object to store
	 * @param object The object to store
	 */
	public function set($name, $object)
	{
		$this->factories[$name] = $object;
	}

	/**
	 * Returns true if an object is currently stored with the given name, false otherwise.
	 *
	 * @param  string The object name
	 * @return boolean True if the object is not null, false otherwise
	 */
	public function has($name)
	{
		return isset($this->factories[$name]);
	}

	/**
	 * Returns the configuration instance.
	 * 
	 * @return coreApplicationConfiguration  The current application configuration instance
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * Retrieve the currently executing action name for this context.
	 *
	 * @return string Action name, otherwise null.
	 */
	public function getActionName()
	{
		return $this->getController()->getActionName();
	}

	/**
	 * Retrieve the currently executing module name for this context.
	 *
	 * @return string Module name, otherwise null.
	 */
	public function getModuleName()
	{
		return $this->getController()->getModuleName();
	}

	/**
	 * Retrieve the current Action instance.
	 * (Replace with getActionStack() if needed)
	 *
	 * @return coreAction  coreAction instance, or null
	 */
	public function getActionInstance()
	{
		return $this->getController()->getActionInstance();
	}

	/**
	 * Retrieve the controller.
	 * 
	 * Currently only used by web front, batch script shouldn't use this.
	 *
	 * @return coreController  A WebController instance.
	 */
	public function getController()
	{
		if (!isset($this->factories['controller']))
		{
			// defaults to web controller
			$controller = new coreWebController($this);
			
			$this->set('controller', $controller);
		}
		
		return $this->factories['controller'];
	}

	/**
	 * Retrieves the response object.
	 * 
	 * Currently only for web front, returns a coreWebResponse.
	 * 
	 * @return 
	 */
	public function getResponse()
	{
		return $this->loadFactory('response');
	}

	/**
	 * Retrieve Symfony's routing object.
	 *
	 * @return sfPatternRouting instance
	 */
	public function getRouting()
	{
		return isset($this->factories['sfPatternRouting']) ? $this->factories['sfPatternRouting'] : null;
	}

	/**
	 * Retrieve the user.
	 *
	 * @return coreUser
	 */
	public function getUser()
	{
		return $this->loadFactory('user');
	}

	/**
	 * Retrieve the request.
	 *
	 * @return coreRequest
	 */
	public function getRequest()
	{
		return $this->loadFactory('request');
	}

	/**
	 * Retrieve the database.
	 *
	 * @return coreDatabase
	 */
	public function getDatabase()
	{
		if (!isset($this->factories['database']))
		{
			// Create database connection when needed
			$db = new coreDatabaseMySQL(coreConfig::get('database_connection'));
			$db->connect();
			$this->set('database', $db);
		}
		
		return isset($this->factories['database']) ? $this->factories['database'] : null;
	}
}
