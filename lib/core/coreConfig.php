<?php
/**
 * coreConfig stores all configuration information for the application.
 *
 * @author     Fabrice Denis
 * @package    Core
 * @subpackage Config
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

class coreConfig
{
  protected static
    $config = array();
  
  /**
   * Retrieves a config parameter.
   *
   * @param string A config parameter name
   * @param mixed  A default config parameter value
   *
   * @return mixed A config parameter value, if the config parameter exists, otherwise null
   */
  public static function get($name, $default = null)
  {
    return isset(self::$config[$name]) ? self::$config[$name] : $default;
  }
  
  /**
   * Indicates whether or not a config parameter exists.
   *
   * @param string A config parameter name
   *
   * @return bool true, if the config parameter exists, otherwise false
   */
  public static function has($name)
  {
    return array_key_exists($name, self::$config);
  }
  
  /**
   * Sets a config parameter.
   *
   * If a config parameter with the name already exists the value will be overridden.
   *
   * @param string A config parameter name
   * @param mixed  A config parameter value
   */
  public static function set($name, $value)
  {
    self::$config[$name] = $value;
  }
  
  /**
   * Sets an array of config parameters.
   *
   * If an existing config parameter name matches any of the keys in the supplied
   * array, the associated value will be overridden.
   *
   * @param array An associative array of config parameters and their associated values
   */
  public static function add($parameters = array())
  {
    self::$config = array_merge(self::$config, $parameters);
  }
  
  /**
   * Retrieves all configuration parameters.
   *
   * @return array An associative array of configuration parameters.
   */
  public static function getAll()
  {
    return self::$config;
  }
  
  /**
   * Clears all current config parameters.
   */
  public static function clear()
  {
    self::$config = null;
    self::$config = array();
  }
}
