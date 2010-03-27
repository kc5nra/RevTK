<?php
/**
 * Base class for exceptions in the core framework.
 * Provides extra debugging information for main context objects.
 * 
 * Uses the following coreConfig settings :
 *   sf_debug                If true, does not display the error 500 page.
 *   sf_web_dir              Where to find custom error500 page, if present.
 *   sf_root_dir             
 *   sf_symfony_lib_dir      
 *   
 * Uses error pages from :
 *   If !_sf_debug :
 *     {sf_web_dir}/errors/error500.php    (custom, if present)
 *     {__FILE__}/errors/error500.php      (default)
 *   Else
 *     {__FILE__}/errors/exception.php     (html)
 *     {__FILE__}/errors/exception.txt     (cli)
 * 
 * 
 * @package    Core
 * @subpackage Exception
 * @author     Fabrice Denis
 * @copyright  Based on Symfony sfException class, by Fabien Potencier (www.symfony-project.org)
 */

class coreException extends Exception
{
  protected
    $wrappedException = null;

  /**
   * Wraps an Exception.
   *
   * @param Exception An Exception instance
   *
   * @return coreException A coreException instance that wraps the given Exception object
   */
  static public function createFromException(Exception $e)
  {
    $exception = new coreException(sprintf('Wrapped %s: %s', get_class($e), $e->getMessage()));
    $exception->setWrappedException($e);
  
    return $exception;
  }
  
  /**
   * Changes the wrapped exception.
   *
   * @param Exception An Exception instance
   */
  public function setWrappedException(Exception $e)
  {
    $this->wrappedException = $e;
  }

  /**
   * Prints the stack trace for this exception.
   */
  public function printStackTrace()
  {
    $exception = is_null($this->wrappedException) ? $this : $this->wrappedException;
    
    if (!coreConfig::get('sf_test'))
    {
      // log all exceptions in php log
      error_log($exception->getMessage());
      
      // clean current output buffer
      while (@ob_end_clean());
      
      ob_start(coreConfig::get('sf_compressed') ? 'ob_gzhandler' : '');
      
      header('HTTP/1.1 500 Internal Server Error');
    }
    
    try
    {
      $this->outputStackTrace($exception);
    }
    catch (Exception $e)
    {
    }
    
    if (!coreConfig::get('sf_test'))
    {
      exit(1);
    }
  }

  /**
   * Gets the stack trace for this exception.
   */
  static protected function outputStackTrace($exception)
  {
    // send an error 500 if not in debug mode
    if (!coreConfig::get('sf_debug'))
    {
      $file = coreConfig::get('sf_web_dir').'/errors/error500.php';

      include is_readable($file) ? $file : dirname(__FILE__).'/errors/error500.php';
    
      return;
    }

    $message = null !== $exception->getMessage() ? $exception->getMessage() : 'n/a';
    $name    = get_class($exception);
    $format  = 0 == strncasecmp(PHP_SAPI, 'cli', 3) ? 'plain' : 'html';
    $traces  = self::getTraces($exception, $format);
    
    // dump main objects values
    $sf_settings = '';
    $settingsTable = $requestTable = $responseTable = $globalsTable = '';
    if (class_exists('coreContext', false) && coreContext::hasInstance())
    {
      $context = coreContext::getInstance();
      $requestTable  = self::formatArrayAsHtml($context->getRequest()->getParameterHolder()->getAll());
    }
    
    include dirname(__FILE__).'/errors/exception.'.($format == 'html' ? 'php' : 'txt');
  }
  
  /**
  * Returns an array of exception traces.
  *
  * @param Exception An Exception implementation instance
  * @param string The trace format (plain or html)
  *
  * @return array An array of traces
  */
  static public function getTraces($exception, $format = 'plain')
  {
    $traceData = $exception->getTrace();
    array_unshift($traceData, array(
      'function' => '',
      'file'     => $exception->getFile() != null ? $exception->getFile() : 'n/a',
      'line'     => $exception->getLine() != null ? $exception->getLine() : 'n/a',
      'args'     => array(),
    ));
    
    $traces = array();
    if ($format == 'html')
    {
      $lineFormat = 'at <strong>%s%s%s</strong>(%s)<br />in <em>%s</em> line %s <a href="#" onclick="toggle(\'%s\'); return false;">...</a><br /><ul id="%s" style="display: %s">%s</ul>';
    }
    else
    {
      $lineFormat = 'at %s%s%s(%s) in %s line %s';
    }
    for ($i = 0, $count = count($traceData); $i < $count; $i++)
    {
      $line = isset($traceData[$i]['line']) ? $traceData[$i]['line'] : 'n/a';
      $file = isset($traceData[$i]['file']) ? $traceData[$i]['file'] : 'n/a';
      $shortFile = preg_replace(array('#^'.preg_quote(coreConfig::get('sf_root_dir')).'#', '#^'.preg_quote(realpath(coreConfig::get('sf_symfony_lib_dir'))).'#'), array('SF_ROOT_DIR', 'SF_SYMFONY_LIB_DIR'), $file);
      $args = isset($traceData[$i]['args']) ? $traceData[$i]['args'] : array();
      $traces[] = sprintf($lineFormat,
        (isset($traceData[$i]['class']) ? $traceData[$i]['class'] : ''),
        (isset($traceData[$i]['type']) ? $traceData[$i]['type'] : ''),
        $traceData[$i]['function'],
        self::formatArgs($args, false, $format),
        $shortFile,
        $line,
        'trace_'.$i,
        'trace_'.$i,
        $i == 0 ? 'block' : 'none',
        self::fileExcerpt($file, $line)
      );
    }
    
    return $traces;
  }
  
  /**
   * Returns an HTML version of an array as YAML.
   *
   * @param array The values array
   *
   * @return string An HTML string
   */
  static protected function formatArrayAsHtml($values)
  {
    return '<pre>'.print_r($values, true).'</pre>';
  }
  
  /**
   * Returns an excerpt of a code file around the given line number.
   *
   * @param string A file path
   * @param int The selected line number
   *
   * @return string An HTML string
   */
  static protected function fileExcerpt($file, $line)
  {
    if (is_readable($file))
    {
      $content = preg_split('#<br />#', highlight_file($file, true));
    
      $lines = array();
      for ($i = max($line - 3, 1), $max = min($line + 3, count($content)); $i <= $max; $i++)
      {
        $lines[] = '<li'.($i == $line ? ' class="selected"' : '').'>'.$content[$i - 1].'</li>';
      }
    
      return '<ol start="'.max($line - 3, 1).'">'.implode("\n", $lines).'</ol>';
    }
  }
  
  /**
   * Formats an array as a string.
   *
   * @param array The argument array
   * @param boolean 
   * @param string The format string (html or plain)
   *
   * @return string
   */
  static protected function formatArgs($args, $single = false, $format = 'html')
  {
    $result = array();
    
    $single and $args = array($args);
    
    foreach ($args as $key => $value)
    {
      if (is_object($value))
      {
        $result[] = ($format == 'html' ? '<em>object</em>' : 'object').'(\''.get_class($value).'\')';
      }
      else if (is_array($value))
      {
        $result[] = ($format == 'html' ? '<em>array</em>' : 'array').'('.self::formatArgs($value).')';
      }
      else if ($value === null)
      {
        $result[] = '<em>null</em>';
      }
      else if (!is_int($key))
      {
        $result[] = "'$key' =&gt; '$value'";
      }
      else
      {
        $result[] = "'".$value."'";
      }
    }
    
    return implode(', ', $result);
  }
}

class coreControllerException extends coreException {}
class coreDatabaseException extends coreException {}

/**
 * Exceptions used by the Symfony classes
 */
class sfException extends coreException {}
class sfConfigurationException extends coreException {}

/**
 * coreStopException is thrown when you want to stop action flow.
 */
class coreStopException extends coreException
{
  // Stops the current action.
  public function printStackTrace() {}
}
