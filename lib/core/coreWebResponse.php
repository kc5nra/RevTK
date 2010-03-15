<?php
/**
 * coreResponse manipulates client response information such as headers, cookies and content.
 * 
 * @todo       Update to Symfony1.1 css/js functions and helpers
 * 
 * @package    Core
 * @subpackage Response
 * @author     Fabrice Denis
 */
class coreResponse
{
  protected
    $options      = array(),
    $content    = '';

  /**
   * Sets the response content
   *
   * @param string Content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }

  /**
   * Gets the current response content
   *
   * @return string Content
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Outputs the response content
   */
  public function sendContent()
  {
    echo $this->getContent();
  }

  /**
   * Sends the content.
   */
  public function send()
  {
    $this->sendContent();
  }
}


/**
 * coreWebResponse class.
 *
 * This class manages web responses. It supports cookies and headers management.
 * 
 * @package    Core
 * @subpackage WebResponse
 * @author     Fabrice Denis
 */
class coreWebResponse extends coreResponse
{
    const
      FIRST  = 'first',
      MIDDLE = '',
      LAST   = 'last',
      ALL    = 'ALL';

  protected
    $cookies    = array(),
    $statusCode  = 200,
    $statusText  = 'OK',
      $headerOnly   = false,
    $headers    = array(),
    $metas    = array(),
    $httpMetas  = array(),
    $positions  = array('first', '', 'last'),
    $stylesheets  = array(),
    $javascripts  = array(),
      $slots        = array();

  /**
   * The list is not exhaustive.
   * 
   * @see   setStatusCode()
   * @link  http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
   */
  static protected $statusTexts = array(
    '200' => 'OK',
    '201' => 'Created',
    '202' => 'Accepted',
    '203' => 'Non-Authoritative Information',
    '204' => 'No Content',
    '205' => 'Reset Content',
    '206' => 'Partial Content',
    
    '300' => 'Multiple Choices',
    '301' => 'Moved Permanently',
    '302' => 'Found',
    '303' => 'See Other',
    '304' => 'Not Modified',
    '305' => 'Use Proxy',
    '306' => '(Unused)',
    '307' => 'Temporary Redirect',
    
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '406' => 'Not Acceptable',
    '407' => 'Proxy Authentication Required',
    '408' => 'Request Timeout',

    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',
    '504' => 'Gateway Timeout',
    '505' => 'HTTP Version Not Supported'
  );

  /**
   * Initializes this coreWebResponse.
   *
   * Available options:
   *
   *  * charset:      The charset to use (utf-8 by default)
   *  * content_type: The content type (text/html by default)
   *
   * @param  array    An array of options
   * @see coreResponse
   */
  public function __construct($options = array())
  {
    $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));
    $this->stylesheets = array_combine($this->positions, array_fill(0, count($this->positions), array()));

    if (!isset($this->options['charset']))
    {
      $this->options['charset'] = coreConfig::get('sf_charset', 'utf-8');
    }

    $this->setContentType(isset($this->options['content_type']) ? $this->options['content_type'] : 'text/html');
  }

  /**
   * Sets if the response consist of just HTTP headers.
   *
   * @param boolean
   */
  public function setHeaderOnly($value = true)
  {
    $this->headerOnly = (boolean) $value;
  }

  /**
   * Sets a cookie.
   *
   * @param string HTTP header name
   * @param string Value for the cookie
   * @param string Cookie expiration period
   * @param string Path
   * @param string Domain name
   * @param boolean If secure
   * @param boolean If uses only HTTP
   *
   * @throws <b>sfException</b> If fails to set the cookie
   */
  public function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
  {
    if ($expire !== null)
    {
      if (is_numeric($expire))
      {
        $expire = (int) $expire;
      }
      else
      {
        $expire = strtotime($expire);
        if ($expire === false || $expire == -1)
        {
          throw new coreException('Your expire parameter is not valid.');
        }
      }
    }

    $this->cookies[] = array(
      'name'    => $name,
      'value'    => $value,
      'expire'  => $expire,
      'path'    => $path,
      'domain'  => $domain,
      'secure'     => $secure ? true : false,
      'httpOnly'   => $httpOnly
    );
  }

  /**
   * Sets response status code.
   *
   * Note: since some status code are rarely, if ever, used, a "Undefined"
   *  status text will be used if the $name is not provided.
   *    
   * @param string HTTP status code
   * @param string HTTP status text
   *
   */
  public function setStatusCode($code, $name = null)
  {
    $this->statusCode = $code;
    
    if ($name !== null)
    {
      $this->statusText = $name;
    }
    else {
      $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : 'Undefined';
    }
  }

  /**
   * Retrieves status code for the current web response.
   *
   * @return string Status code
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }

  /**
   * Sets a HTTP header.
   *
   * @param string  HTTP header name
   * @param string  Value (if null, remove the HTTP header)
   * @param boolean   Replace value or append to it (see addHttpMeta)
   *
   */
  public function setHttpHeader($name, $value, $replace = true)
  {
    $name = $this->normalizeHeaderName($name);

    if (is_null($value))
    {
      unset($this->headers[$name]);

      return;
    }

    if ('Content-Type' == $name)
    {
      if ($replace || !$this->getHttpHeader('Content-Type', null))
      {
        $this->setContentType($value);
      }

      return;
    }

    if (!$replace)
    {
      $current = isset($this->headers[$name]) ? $this->headers[$name] : '';
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->headers[$name] = $value;
  }

  /**
  * Gets HTTP header current value.
  *
  * @return array
  */
  public function getHttpHeader($name, $default = null)
  {
    $name = $this->normalizeHeaderName($name);
  
    return isset($this->headers[$name]) ? $this->headers[$name] : $default;
  }


  /**
   * Sets response content type.
   *
   * @param string Content type
   *
   */
  public function setContentType($value)
  {
    // add charset if needed (only on text content)
    if (false === stripos($value, 'charset') && (0 === stripos($value, 'text/') || strlen($value) - 3 === strripos($value, 'xml')))
    {
      $value .= '; charset='.$this->options['charset'];
    }

    $this->headers['Content-Type'] = $value;
  }

  /**
   * Gets response content type.
   *
   * @return array
   */
  public function getContentType()
  {
    return $this->getHttpHeader('Content-Type');
  }

  /**
   * Send HTTP headers and cookies.
   *
   */
  public function sendHttpHeaders()
  {
    if (coreConfig::get('sf_test'))
    {
//      return;
    }
//DBG::printr($this->headers);

    // status
    $status = 'HTTP/1.1 '.$this->statusCode.' '.$this->statusText;
    header($status);

    // headers
    foreach ($this->headers as $name => $value)
    {
      header($name.': '.$value);
    }

    // cookies
    foreach ($this->cookies as $cookie)
    {
      if (version_compare(phpversion(), '5.2', '>='))
      {
        setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
      }
      else
      {
        setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
      }
    }
  }

  /**
   * Sends the HTTP headers and the content.
   */
  public function send()
  {
    // compress output
      ob_start(coreConfig::get('sf_compressed') ? 'ob_gzhandler' : null);

    $this->sendHttpHeaders();

    if (!$this->headerOnly)
    {
      parent::sendContent();
    }

    ob_end_flush();
  }

  /**
   * Retrieves a normalized Header.
   *
   * @param string Header name
   *
   * @return string Normalized header
   */
  protected function normalizeHeaderName($name)
  {
    return preg_replace('/\-(.)/e', "'-'.strtoupper('\\1')", strtr(ucfirst(strtolower($name)), '_', '-'));
  }

  /**
   * Retrieves meta headers for the current web response.
   *
   * @return string Meta headers
   */
  public function getHttpMetas()
  {
    return $this->httpMetas;
  }

  /**
   * Adds a HTTP meta header (http-equiv).
   * 
   * Set $replace to false to append a value to an previously set http meta.
   * For example:
   *   
   *   ->addHttpMeta('accept-language', 'en');
   *   ->addHttpMeta('accept-language', 'fr', false);
   *   
   *   ->getHttpHeader('accept-language');
   *   => 'en, fr'
   * 
   * @param string  Key to replace
   * @param string  HTTP meta header value (if null, remove the HTTP meta)
   * @param boolean   Replace value or append to it
   */
  public function addHttpMeta($key, $value, $replace = true)
  {
    $key = $this->normalizeHeaderName($key);

    // set HTTP header
    $this->setHttpHeader($key, $value, $replace);

    if (is_null($value))
    {
      unset($this->httpMetas[$key]);

      return;
    }

    if ('Content-Type' == $key)
    {
      $value = $this->getContentType();
    }
    elseif (!$replace)
    {
      $current = isset($this->httpMetas[$key]) ? $this->httpMetas[$key] : '';
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->httpMetas[$key] = $value;
  }

  /**
   * Retrieves all meta headers.
   *
   * @return array List of meta headers
   */
  public function getMetas()
  {
    return $this->metas;
  }

  /**
   * Adds a meta header.
   *
   * @param string  Name of the header
   * @param string  Meta header value (if null, remove the meta)
   * @param boolean true if it's replaceable
   * @param boolean true for escaping the header
   */
  public function addMeta($key, $value, $replace = true, $escape = true)
  {
    $key = strtolower($key);

    if (is_null($value))
    {
      unset($this->metas[$key]);

      return;
    }

    // see include_metas() in AssetHelper
    if ($escape)
    {
      $value = htmlspecialchars($value, ENT_QUOTES, $this->options['charset']);
    }

    $current = isset($this->metas[$key]) ? $this->metas[$key] : null;
    if ($replace || !$current)
    {
      $this->metas[$key] = $value;
    }
  }

  /**
   * Retrieves title for the current web response.
   *
   * @return string Title
   */
  public function getTitle()
  {
    return isset($this->metas['title']) ? $this->metas['title'] : '';
  }

  /**
   * Sets title for the current web response.
   *
   * @param string Title name
   * @param boolean true, for escaping the title
   */
  public function setTitle($title, $replace = true, $escape = true)
  {
    $this->addMeta('title', $title, $replace, $escape);
  }

  /**
   * Returns the available position names for stylesheets and javascripts in order.
   *
   * @return array An array of position names
   */
  public function getPositions()
  {
    return $this->positions;
  }

  /**
   * Retrieves stylesheets for the current web response.
   *
   * @param string  Position
   *
   * @return string Stylesheets
   */
  public function getStylesheets($position = '')
  {
    if ($position == 'ALL')
    {
      return $this->stylesheets;
    }

    $this->validatePosition($position);

    return isset($this->stylesheets[$position]) ? $this->stylesheets[$position] : array();
  }

  /**
   * Adds a stylesheet to the current web response.
   * 
   * Examples:
   *  addStylesheet('base.css');
   *  addStylesheet('base.css', 'first');
   *  addStylesheet('print.css', 'last', array('media' => 'print');
   *
   * @param string Stylesheet
   * @param string Position
   * @param string Options
   */
  public function addStylesheet($css, $position = '', $options = array())
  {
    $this->validatePosition($position);
    
    $this->stylesheets[$position][$css] = $options;
  }

  /**
   * Retrieves javascript code from the current web response.
   *
   * @param string  Position
   *
   * @return string Javascript code
   */
  public function getJavascripts($position = self::ALL)
  {
    if ($position === self::ALL)
    {
      return $this->javascripts;
    }

    $this->validatePosition($position);

    return isset($this->javascripts[$position]) ? $this->javascripts[$position] : array();
  }

  /**
   * Adds javascript code to the current web response.
   *
   * @param string Javascript code
   * @param string Position
   * @param string Javascript options
   */
  public function addJavascript($js, $position = '', $options = array())
  {
    $this->validatePosition($position);

    $this->javascripts[$position][$js] = $options;
  }

  /**
   * Retrieves slots from the current web response.
   *
   * @return string Javascript code
   */
  public function getSlots()
  {
    return $this->slots;
  }

  /**
   * Sets a slot content.
   *
   * @param string $name     Slot name
   * @param string $content  Content
   */
  public function setSlot($name, $content)
  {
    $this->slots[$name] = $content;
  }

  /**
   * Cleans HTTP headers from the current web response.
   */
  public function clearHttpHeaders()
  {
    $this->headers = array();
  }

  /**
   * Validate a position name.
   *
   * @throws InvalidArgumentException if the position is not available
   */
  protected function validatePosition($position)
  {
    if (!in_array($position, $this->positions, true))
    {
      throw new coreException(sprintf('The position "%s" does not exist (available positions: %s).', $position, implode(', ', $this->positions)));
    }
  }
}
