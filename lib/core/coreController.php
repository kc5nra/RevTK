<?php
/**
 * 
 * 
 * @author     Fabrice Denis
 * @package    Core
 * @subpackage Controller
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */


/**
 * Controller
 * 
 * @package Core
 */
abstract class coreController
{
	protected
 	  $context           = null,
	  $controllerClasses = array();

	/**
	 * Note: no action stack, to be implemented if really necessary.
	 */
	protected
	  $actionName     = null,
      $moduleName     = null,
	  $currentAction  = null;

	protected function __construct(coreContext $context)
	{
		$this->context = $context;
	}
	
	/**
	 * Retrieves the executing action's name.
	 *
	 * @return string An action name
	 */
	public function getActionName()
	{
		return $this->actionName;
	}

	/**
	 * Retrieves the executing module's name.
	 *
	 * @return string A module name
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

	/**
	 * Retrieves the running action instance.
	 * This should be replaced with getActionStack() if needed.
	 *
	 * @return coreAction  Action instance, or null if not set
	 */
	public function getActionInstance()
	{
		return $this->currentAction;
	}

	/**
	 * Indicates whether or not a module has a specific component.
	 *
	 * @param string A module name
	 * @param string An component name
	 *
	 * @return bool true, if the component exists, otherwise false
	 */
	public function componentExists($moduleName, $componentName)
	{
	  	return $this->controllerExists($moduleName, $componentName, 'component', false);
	}
	
	/**
	 * Indicates whether or not a module has a specific action.
	 *
	 * @param string A module name
	 * @param string An action name
	 *
	 * @return bool true, if the action exists, otherwise false
	 */
	public function actionExists($moduleName, $actionName)
	{
	  	return $this->controllerExists($moduleName, $actionName, 'action', false);
	}

	/**
	 * @param string  The name of the module
	 * @param string  The name of the controller within the module
	 * @param string  Either 'action' or 'component' depending on the type of controller to look for
	 * @param boolean Whether to throw exceptions if the controller doesn't exist
	 *
	 * @throws sfConfigurationException thrown if the module is not enabled
	 * @throws sfControllerException thrown if the controller doesn't exist and the $throwExceptions parameter is set to true
	 *
	 * @return boolean true if the controller exists, false otherwise
	 */
	private function controllerExists($moduleName, $controllerName, $extension, $throwExceptions)
	{
	    $dirs = $this->context->getConfiguration()->getControllerDirs($moduleName);
	    foreach ($dirs as $dir)
	    {
			// one action per file or one file for all actions
			$classFile   = strtolower($extension);
			$classSuffix = ucfirst($classFile);
			$file        = $dir.'/'.$controllerName.$classSuffix.'.php';
			if (is_readable($file))
			{
			  // action class exists
			  require_once($file);
			
			  $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix] = $controllerName.$classSuffix;
			
			  return true;
			}
			
			$module_file = $dir.'/'.$classFile.'s.php';
			if (is_readable($module_file))
			{
			  // module class exists
			  require_once($module_file);
			
			  if (!class_exists($moduleName.$classSuffix.'s', false))
			  {
			    if ($throwExceptions)
			    {
			      throw new coreControllerException(sprintf('There is no "%s" class in your action file "%s".', $moduleName.$classSuffix.'s', $module_file));
			    }
			
			    return false;
			  }

			  // action is defined in this class?
			  if (!in_array('execute'.ucfirst($controllerName), get_class_methods($moduleName.$classSuffix.'s')))
			  {
			    if ($throwExceptions)
			    {
			      throw new coreControllerException(sprintf('There is no "%s" method in your action class "%s".', 'execute'.ucfirst($controllerName), $moduleName.$classSuffix.'s'));
			    }
			
			    return false;
			  }

			  $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix] = $moduleName.$classSuffix.'s';
			  return true;
			}
		}
	}

	/**
	* Retrieves a coreAction implementation instance.
	*
	* @param  string A module name
	* @param  string An action name
	*
	* @return coreAction A coreAction implementation instance, if the action exists, otherwise null
	*/
	public function getAction($moduleName, $actionName)
	{
		return $this->getController($moduleName, $actionName, 'action');
	}
	
	/**
	* Retrieves a coreComponent implementation instance.
	*
	* @param  string A module name
	* @param  string A component name
	*
	* @return coreComponent A coreComponent implementation instance, if the component exists, otherwise null
	*/
	public function getComponent($moduleName, $componentName)
	{
		return $this->getController($moduleName, $componentName, 'component');
	}
	
	/**
	* Retrieves a controller implementation instance.
	*
	* @param  string A module name
	* @param  string A component name
	* @param  string  Either 'action' or 'component' depending on the type of controller to look for
	*
	* @return object A controller implementation instance, if the controller exists, otherwise null
	*
	* @see getComponent(), getAction()
	*/
	protected function getController($moduleName, $controllerName, $extension)
	{
		$classSuffix = ucfirst(strtolower($extension));

		if (!isset($this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix]))
		{
			$this->controllerExists($moduleName, $controllerName, $extension, true);
		}
		
		$class = $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix];
		
		// fix for same name classes
		$moduleClass = $moduleName.'_'.$class;
		
		if (class_exists($moduleClass, false))
		{
			$class = $moduleClass;
		}
		
		return new $class($this->context, $moduleName, $controllerName);
	}

	/**
	 * Forward the request to another action.
	 * 
	 * @param string  A module name
	 * @param string  An action name
	 * 
	 * @return 
	 */
	public function forward($moduleName, $actionName)
	{
    // replace unwanted characters
    $moduleName = preg_replace('/[^a-z0-9_]+/i', '', $moduleName);
    $actionName = preg_replace('/[^a-z0-9_]+/i', '', $actionName);

		if (!$this->actionExists($moduleName, $actionName))
		{
			throw new coreError404Exception(sprintf('Action "%s/%s" does not exist.', $moduleName, $actionName));
		}

		// create an instance of the action
		$actionInstance = $this->getAction($moduleName, $actionName);

    // remember last action entry
		$this->actionName = $actionName;
		$this->moduleName = $moduleName;
		$this->currentAction = $actionInstance;

		// set headers if 404 action
		if ($moduleName == coreConfig::get('error_404_module') && $actionName == coreConfig::get('error_404_action'))
		{
			$this->context->getResponse()->setStatusCode(404);
			$this->context->getResponse()->setHttpHeader('Status', '404 Not Found');
		}
		
		// if this action is secure, run security filter
		if ($actionInstance->isSecure())
		{
			// check if the user is authenticated and has the credentials to execute this action
			$filter = new coreBasicSecurityFilter($this->context, $actionInstance);
			$filter->execute();
		}

		// execute action
		$viewName = $actionInstance->execute($this->context->getRequest());

		// by default, no return value from action means coreView::SUCCESS
		if (is_null($viewName)) {
			$viewName = coreView::SUCCESS;
		}

		$response = $this->context->getResponse();

		if ($viewName!==coreView::NONE)
		{
			// get the view instance
	    $viewInstance = new coreView($this->context, $moduleName, $actionName, $viewName);
	
			// get the view configuration
			$viewConfigHandler = new coreViewConfigHandler();
			$viewConfigHandler->mergeConfig($moduleName, $actionName, $viewName);
			$viewConfigHandler->applyConfig($viewInstance);
	
			// if there is a layout, decorate view with it
			$decoratorTemplate = coreConfig::get('core.view.'.$this->getModuleName().'_'.$this->getActionName().'_layout');
			if ($decoratorTemplate !== null)
			{
				$viewInstance->setDecoratorTemplate($decoratorTemplate);
			}
	
	    // pass attributes to the view and render
			$viewAttributes = $actionInstance->getVarHolder()->getAll();
	    $viewInstance->getParameterHolder()->add($viewAttributes);
			$viewData = $viewInstance->render();
			if ($viewData !== false)
			{
				$response->setContent($viewData);
			}
		}
		
		$response->send();
	}
}


/**
 * The web front-end controller.
 * 
 * @package Core
 */
class coreWebController extends coreController
{
	protected
	  $request  = null,
	  $routing  = null;

	public function __construct(coreContext $context)
	{
		parent::__construct($context);

		$this->request = $context->getRequest();
		$this->response = $context->getResponse();
	}

	/**
	 * Generates an URL from an array of parameters.
	 *
	 * @param mixed   An associative array of URL parameters or an internal URI as a string.
	 * @param boolean Whether to generate an absolute URL
	 *
	 * @return string A URL to a symfony resource
	 */
	public function genUrl($parameters = array(), $absolute = false)
	{
		// absolute URL or internal URL?
		if (!is_array($parameters) && preg_match('#^[a-z][a-z0-9\+.\-]*\://#i', $parameters))
		{
		  	return $parameters;
		}
		
		if (!is_array($parameters) && $parameters == '#')
		{
		  	return $parameters;
		}
		
		$url = '';
		if (!coreConfig::get('no_script_name'))
		{
		  	$url = $this->context->getRequest()->getScriptName();
		}
		else if ($relative_url_root = $this->context->getRequest()->getRelativeUrlRoot())
		{
		  	$url = $relative_url_root;
		}
		
		$route_name = '';
		$fragment = '';
		
		if (!is_array($parameters))
		{
			// strip fragment
			if (false !== ($pos = strpos($parameters, '#')))
			{
				$fragment = substr($parameters, $pos + 1);
				$parameters = substr($parameters, 0, $pos);
			}
		
			list($route_name, $parameters) = $this->convertUrlStringToParameters($parameters);
		}

		if (coreConfig::get('url_format') == 'PATH')
		{
		  	// use PATH format
		  	$divider = '/';
		  	$equals  = '/';
		  	$querydiv = '/';
		}
		else
		{
		  	// use GET format
		  	$divider = ini_get('arg_separator.output');
		  	$equals  = '=';
		  	$querydiv = '?';
		}
		
		// routing to generate path
		$url .= $this->context->getRouting()->generate($route_name, $parameters, $querydiv, $divider, $equals);

		if ($absolute)
		{
		  	$request = $this->context->getRequest();
		  	$url = 'http'.($request->isSecure() ? 's' : '').'://'.$request->getHost().$url;
		}
		
		if ($fragment)
		{
		  	$url .= '#'.$fragment;
		}
		
		return $url;
	}

	/**
	 * Converts an internal URI string to an array of parameters.
	 *
	 * @param string An internal URI
	 * @return array An array of parameters
	 */
	public function convertUrlStringToParameters($url)
	{
		$givenUrl = $url;
		
		$params       = array();
		$query_string = '';
		$route_name   = '';
		
		// empty url?
		if (!$url)
		{
		  	$url = '/';
		}
		
		// we get the query string out of the url
		if ($pos = strpos($url, '?'))
		{
		  	$query_string = substr($url, $pos + 1);
		  	$url = substr($url, 0, $pos);
		}
		
		// 2 url forms
		// @route_name?key1=value1&key2=value2...
		// module/action?key1=value1&key2=value2...
		
		// first slash optional
		if ($url[0] == '/')
		{
		  	$url = substr($url, 1);
		}
		
		
		// route_name?
		if ($url[0] == '@')
		{
		  	$route_name = substr($url, 1);
		}
		else if (false !== strpos($url, '/'))
		{
		  	list($params['module'], $params['action']) = explode('/', $url);
		}
		else
		{
		  	throw new InvalidArgumentException(sprintf('An internal URI must contain a module and an action (module/action) ("%s" given).', $givenUrl));
		}
		
		// split the query string
		if ($query_string)
		{
		  	$matched = preg_match_all('/
		    ([^&=]+)            # key
		    =                   # =
		    (.*?)               # value
		    (?:
		      (?=&[^&=]+=) | $  # followed by another key= or the end of the string
		    )
            /x', $query_string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		  	
			foreach ($matches as $match)
		  	{
		    	$params[$match[1][0]] = $match[2][0];
		  	}
		
		  	// check that all string is matched
		  	if (!$matched)
		  	{
        		throw new sfException(sprintf('Unable to parse query string "%s".', $query_string));
		  	}
		}
		
		return array($route_name, $params);
	}

	/**
	 * Redirects the request to another URL.
	 *
	 * @param string An existing URL
	 * @param int    A delay in seconds before redirecting. This is only needed on
	 *				 browsers that do not support HTTP headers
	 * @param int    The status code
	 */
	public function redirect($url, $delay = 0, $statusCode = 302)
	{
		$url = $this->genUrl($url, true);

		// redirect
		$response = $this->context->getResponse();
		$response->clearHttpHeaders();
		$response->setStatusCode($statusCode);
		$response->setHttpHeader('Location', $url);
		$response->setContent(sprintf('<html><head><meta http-equiv="refresh" content="%d;url=%s"/></head></html>', $delay, htmlspecialchars($url, ENT_QUOTES, coreConfig::get('sf_charset'))));
		$response->send();
	}

	/**
	 * Bridge between the Core framework configuration and Symfony 1.1 Url Routing.
	 * 
	 * Load the url routing config from settings.php, and add the routes to
	 * Symfony's sfRouting object with the connect() method.
	 * 
	 * @todo   Use a cache with sfPatternRouting and evaluate speed gains.
	 * 
	 * @return sfPatternRouting
	 */
	protected function initializeSymfonyRouting()
	{
		$dispatcher = new sfEventDispatcher();
		$sf_routing = new sfPatternRouting($dispatcher);
		coreContext::getInstance()->set('sfPatternRouting', $sf_routing);

		$app_routes = coreConfig::get('routing_config');

		// Add routes to the sfPatternRouting object
		foreach($app_routes['routes'] as $name => $route)
		{
			$defaults = isset($route['param']) ? $route['param'] : array();
			$requirements = isset($route['requirements']) ? $route['requirements'] : array();
			$sf_routing->connect($name, $route['url'], $defaults, $requirements);
		}

		return $sf_routing;
	}


	/**
	 * This is called by the front web controller to dispatch the request.
	 * 
	 * @return 
	 */
	public function dispatch()
	{
/* testing without mod_rewrite! */
//$request_uri = trim($this->context->getRequest()->getParameter('url', ''), '/');
//DBG::printr($this->context->getRequest()->getParameterHolder()->getAll());
		try
		{
			$request_uri = $this->request->getPathInfo();

			// use routes to determine module, action and url parameters
			$sf_routing = $this->initializeSymfonyRouting();
			$params = $sf_routing->parse($request_uri);

			// adds request url parameters to request object!
			$this->request->getParameterHolder()->add($params);
	
			// determine our module and action
			$moduleName = $this->request->getParameter('module');
			$actionName = $this->request->getParameter('action');
	
			if (empty($moduleName) || empty($actionName))
			{
				throw new coreError404Exception(sprintf('Empty module and/or action after parsing the URL "%s" (%s/%s).', $request->getPathInfo(), $moduleName, $actionName));
			}

			$this->forward($moduleName, $actionName);
		}
		catch (coreException $e)
		{
		  $e->printStackTrace();
		}
		catch (Exception $e)
		{
		  coreException::createFromException($e)->printStackTrace();
		}
	}
}
