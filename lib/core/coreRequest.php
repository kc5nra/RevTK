<?php
/**
 * Request object to handle GET/POST data for the Console and Web Controllers.
 * 
 * The Request object is initialized as part of the Ajax and Web Controllers bootstrap.
 * All Request parameters have the backslashes stripped off so you don't have to worry
 * about magic_quotes_gpc.
 * 
 * Request object is available also in the Component class.
 * 
 * @package Core
 * @author  Fabrice Denis
 * @copyright Based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

class coreRequest
{
  const GET = 1;
  const POST = 2;

  protected
    $errors          = array(),
    $method          = null,
    $parameterHolder = null;

  /**
   * Class constructor.
   *
   */
  public function __construct()
  {
    coreContext::getInstance()->set('request', $this);

    // initialize parameter holder
    $this->parameterHolder = new sfParameterHolder();
  }

  /**
   * Returns one of the Request method constants (eg.Request::GET)
   * 
   * @return int Method constant (Request::GET, Request::POST, ...)
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * Returns requets method name in text (eg 'GET' or 'POST').
   * 
   * @return string
   */
  public function getMethodName()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  public function setMethod($methodCode)
  {
    assert('$methodCode===self::GET || $methodCode===self::POST');
    $this->method = $methodCode;
    return;
  }

  /**
   * Retrieves an error message.
   * 
   * @param string Key
   * 
   * @return string An error message or null if the error doesn't exist
   */
  public function getError($name)
  {
    return isset($this->errors[$name]) ? $this->errors[$name] : null;
  }

  /**
   * Retrieves all errors for this request.
   * 
   * @return array Associative array of name => message
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Removes an error.
   *
   * @param string An error name
   *
   * @return string An error message, if the error was removed, otherwise null
   */
  public function removeError($name)
  {
    $retval = null;
    if (isset($this->errors[$name]))
    {
        $retval = $this->errors[$name];
        unset($this->errors[$name]);
    }

    return $retval;
  }

  /**
   * Sets an error message.
   * 
   * @param string Key
   * @param string Error message
   */
  public function setError($name, $message)
  {
    $this->errors[$name] = $message;
  }
  
  /**
   * Sets an array of errors.
   *
   * If an existing error name matches any of the keys in the supplied
   * array, the associated message will be overridden.
   *
   * @param array An associative array of errors and their associated messages
   */
  public function setErrors($errors)
  {
    $this->errors = array_merge($this->errors, $errors);
  }
  
  /**
   * Checks whether or not any errors exists.
   * 
   * @return boolean True if any error exists, false otherwise.
   */
  public function hasErrors()
  {
    return count($this->errors) > 0;
  }
  
  /**
   * Checks if an error exists for given key.
   * 
   * @return boolean True if an error exists, false otherwise.
   */
  public function hasError($name)
  {
    return array_key_exists($name, $this->errors);
  }

  /**
   * Retrieves the parameters for the current request.
   *
   * @return sfParameterHolder The parameter holder
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Retrieves a paramater for the current request.
   *
   * @param string Parameter name
   * @param string Parameter default value
   *
   */
  public function getParameter($name, $default = null)
  {
    return $this->parameterHolder->get($name, $default);
  }

  /**
   * Indicates whether or not a parameter exist for the current request.
   *
   * @param string Parameter name
   *
   * @return boolean true, if the paramater exists otherwise false
   */
  public function hasParameter($name)
  {
    return $this->parameterHolder->has($name);
  }

  /**
   * Sets a parameter for the current request.
   *
   * @param string Parameter name
   * @param string Parameter value
   *
   */
  public function setParameter($name, $value)
  {
    $this->parameterHolder->set($name, $value);
  }
}


/**
 * The web request parses input from the request and stores them as parameters.
 * 
 * @return 
 */
class coreWebRequest extends coreRequest
{
  protected
    $pathInfoArray   = null,
    $relativeUrlRoot = null;

  /**
   * Initialize this web request.
   * 
   * @return 
   */
  public function __construct()
  {
    parent::__construct();
    
    if (isset($_SERVER['REQUEST_METHOD']))
    {
      switch ($_SERVER['REQUEST_METHOD'])
      {
        case 'GET':
          $this->setMethod(self::GET);
          break;
        
        case 'POST':
          $this->setMethod(self::POST);
          break;

        default:
          $this->setMethod(self::GET);
      }
    }
    else
    {
      // set the default method
      $this->setMethod(self::GET);
    }

    // GET parameters
    $this->getParameters = get_magic_quotes_gpc() ? coreToolkit::stripslashesDeep($_GET) : $_GET;
    $this->parameterHolder->add($this->getParameters);
    
    // POST parameters
    $this->postParameters = get_magic_quotes_gpc() ? coreToolkit::stripslashesDeep($_POST) : $_POST;
    $this->parameterHolder->add($this->postParameters);
  }

  /**
   * Returns the value of a given HTTP header.
   * 
   * @param string $name  HTTP header key
   * @param object $prefix[optional] Defaults to HTTP
   * 
   * @return 
   */
  public function getHttpHeader($name, $prefix = 'http')
  {
    if ($prefix)
    {
      $prefix = strtoupper($prefix).'_';
    }

    $name = $prefix.strtoupper(strtr($name, '-', '_'));

    $pathArray = $this->getPathInfoArray();

    return isset($pathArray[$name]) ? stripslashes($pathArray[$name]) : null;
  }

  /**
   * Gets a cookie value.
   *
   * @return mixed
   */
  public function getCookie($name, $defaultValue = null)
  {
    $retval = $defaultValue;

    if (isset($_COOKIE[$name]))
    {
      $retval = get_magic_quotes_gpc() ? stripslashes($_COOKIE[$name]) : $_COOKIE[$name];
    }

    return $retval;
  }

  /**
   * Returns true if the current request is secure (HTTPS protocol).
   *
   * @return boolean
   */
  public function isSecure()
  {
    $pathArray = $this->getPathInfoArray();

    return (
      (isset($pathArray['HTTPS']) && (strtolower($pathArray['HTTPS']) == 'on' || strtolower($pathArray['HTTPS']) == 1))
      ||
      (isset($pathArray['HTTP_X_FORWARDED_PROTO']) && strtolower($pathArray['HTTP_X_FORWARDED_PROTO']) == 'https')
    );
  }

  /**
   * Retrieves relative root url.
   *
   * @return string URL
   */
  public function getRelativeUrlRoot()
  {
    if ($this->relativeUrlRoot === null)
    {
      $this->relativeUrlRoot = coreConfig::get('relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', $this->getScriptName()));
    }

    return ''; //$this->relativeUrlRoot;
  }

  /**
   * Returns the array that contains all request information ($_SERVER or $_ENV).
   *
   * @return  array Path information
   */
  public function getPathInfoArray()
  {
    if (!$this->pathInfoArray)
    {
      $this->pathInfoArray =& $_SERVER;
    }

    return $this->pathInfoArray;
  }

  /**
   * Retrieves the uniform resource identifier for the current web request.
   *
   * @return string Unified resource identifier
   */
  public function getUri()
  {
    $pathArray = $this->getPathInfoArray();

    if ($this->isAbsUri())
    {
      return $pathArray['REQUEST_URI'];
    }

    return $this->getUriPrefix().$pathArray['REQUEST_URI'];
  }

  /**
   * See if the client is using absolute uri
   *
   * @return boolean true, if is absolute uri otherwise false
   */
  public function isAbsUri()
  {
    $pathArray = $this->getPathInfoArray();

    return preg_match('/^http/', $pathArray['REQUEST_URI']);
  }

  /**
   * Returns Uri prefix, including protocol, hostname and server port.
   *
   * @return string Uniform resource identifier prefix
   */
  public function getUriPrefix()
  {
    $pathArray = $this->getPathInfoArray();
    if ($this->isSecure())
    {
      $standardPort = '443';
      $proto = 'https';
    }
    else
    {
      $standardPort = '80';
      $proto = 'http';
    }

    $port = $pathArray['SERVER_PORT'] == $standardPort || !$pathArray['SERVER_PORT'] ? '' : ':'.$pathArray['SERVER_PORT'];

    return $proto.'://'.$pathArray['SERVER_NAME'].$port;
  }

  /**
   * Retrieves the path info for the current web request.
   *
   * @return string Path info
   */
  public function getPathInfo()
  {
    $pathInfo = '';

    $pathArray = $this->getPathInfoArray();

    // simulate PATH_INFO if needed
    $sf_path_info_key = 'PATH_INFO';
    if (!isset($pathArray[$sf_path_info_key]) || !$pathArray[$sf_path_info_key])
    {
      if (isset($pathArray['REQUEST_URI']))
      {
        $script_name = $this->getScriptName();
        $uri_prefix = $this->isAbsUri() ? $this->getUriPrefix() : '';
        $pathInfo = preg_replace('/^'.preg_quote($uri_prefix, '/').'/','',$pathArray['REQUEST_URI']);
        $pathInfo = preg_replace('/^'.preg_quote($script_name, '/').'/', '', $pathInfo);
        $prefix_name = preg_replace('#/[^/]+$#', '', $script_name);
        $pathInfo = preg_replace('/^'.preg_quote($prefix_name, '/').'/', '', $pathInfo);
        $query_string = isset($pathArray['QUERY_STRING']) ? $pathArray['QUERY_STRING'] : '';
        $pathInfo = preg_replace('/'.preg_quote($query_string, '/').'$/', '', $pathInfo);
      }
    }
    else
    {
      $pathInfo = $pathArray[$sf_path_info_key];
    }

    if (!$pathInfo)
    {
      $pathInfo = '/';
    }

    return $pathInfo;
  }

  /**
   * Returns referer.
   *
   * @return  string
   */
  public function getReferer()
  {
    $pathArray = $this->getPathInfoArray();

    return isset($pathArray['HTTP_REFERER']) ? $pathArray['HTTP_REFERER'] : '';
  }

  /**
   * Returns current host name.
   *
   * @return  string
   */
  public function getHost()
  {
    $pathArray = $this->getPathInfoArray();

    return isset($pathArray['HTTP_X_FORWARDED_HOST']) ? $pathArray['HTTP_X_FORWARDED_HOST'] : (isset($pathArray['HTTP_HOST']) ? $pathArray['HTTP_HOST'] : '');
  }

  /**
   * Returns current script name.
   *
   * @return  string
   */
  public function getScriptName()
  {
    $pathArray = $this->getPathInfoArray();

    return isset($pathArray['SCRIPT_NAME']) ? $pathArray['SCRIPT_NAME'] : (isset($pathArray['ORIG_SCRIPT_NAME']) ? $pathArray['ORIG_SCRIPT_NAME'] : '');
  }
}
