<?php

/*
 * Note: Added isEscaped() and toArray() methods to the Symfony class.
 * This is to make it behave like sfViewParameterHolder which otherwise we don't need.
 */
 
/**
 * sfParameterHolder provides a base class for managing parameters.
 *
 * Parameters, in this case, are used to extend classes with additional data
 * that requires no additional logic to manage.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfParameterHolder.class.php 7792 2008-03-09 22:06:59Z fabien $
 */
class sfParameterHolder implements Serializable
{
  protected $parameters = array();

  /**
   * The constructor for sfParameterHolder.
   */
  public function __construct()
  {
  }

  /**
   * Clears all parameters associated with this request.
   */
  public function clear()
  {
    $this->parameters = array();
  }

  /**
   * Retrieves a parameter.
   *
   * @param string A parameter name
   * @param mixed  A default parameter value
   *
   * @return mixed A parameter value, if the parameter exists, otherwise null
   */
  public function & get($name, $default = null)
  {
    if (isset($this->parameters[$name]))
    {
      $value = & $this->parameters[$name];
    }
    else
    {
      $value = $default;
      //$value = sfToolkit::getArrayValueForPath($this->parameters, $name, $default);
    }

    return $value;
  }

  /**
   * Retrieves an array of parameter names.
   *
   * @return array An indexed array of parameter names
   */
  public function getNames()
  {
    return array_keys($this->parameters);
  }

  /**
   * Retrieves an array of parameters.
   *
   * @return array An associative array of parameters
   */
  public function & getAll()
  {
    return $this->parameters;
  }

  /**
   * Indicates whether or not a parameter exists.
   *
   * @param string A parameter name
   *
   * @return bool true, if the parameter exists, otherwise false
   */
  public function has($name)
  {
		if (array_key_exists($name, $this->parameters))
		{
		  return true;
		}
		/* Symfony 1.2 accepts 'token names' (eg. ->has('lions[2]') )
		else
		{
		  return sfToolkit::hasArrayValueForPath($this->parameters, $name);
		}
		*/
		
		return false;
  }

  /**
   * Remove a parameter.
   *
   * @param string A parameter name
   * @param mixed  A default parameter value
   *
   * @return string A parameter value, if the parameter was removed, otherwise null
   */
  public function remove($name, $default = null)
  {
    $retval = $default;

    if (array_key_exists($name, $this->parameters))
    {
      $retval = $this->parameters[$name];
      unset($this->parameters[$name]);
    }

    return $retval;
  }

  /**
   * Sets a parameter.
   *
   * If a parameter with the name already exists the value will be overridden.
   *
   * @param string A parameter name
   * @param mixed  A parameter value
   */
  public function set($name, $value)
  {
    $this->parameters[$name] = $value;
  }

  /**
   * Sets a parameter by reference.
   *
   * If a parameter with the name already exists the value will be overridden.
   *
   * @param string A parameter name
   * @param mixed  A reference to a parameter value
   */
  public function setByRef($name, & $value)
  {
    $this->parameters[$name] =& $value;
  }

  /**
   * Sets an array of parameters.
   *
   * If an existing parameter name matches any of the keys in the supplied
   * array, the associated value will be overridden.
   *
   * @param array An associative array of parameters and their associated values
   */
  public function add($parameters)
  {
    if (is_null($parameters))
    {
      return;
    }

    foreach ($parameters as $key => $value)
    {
      $this->parameters[$key] = $value;
    }
  }

  /**
   * Sets an array of parameters by reference.
   *
   * If an existing parameter name matches any of the keys in the supplied
   * array, the associated value will be overridden.
   *
   * @param array An associative array of parameters and references to their associated values
   */
  public function addByRef(& $parameters)
  {
    foreach ($parameters as $key => &$value)
    {
      $this->parameters[$key] =& $value;
    }
  }

  /**
   * Returns true if the current object acts as an escaper.
   *
   * @return Boolean true if the current object acts as an escaper, false otherwise
   */
  public function isEscaped()
  {
    return false;
  }

  /**
   * Returns an array representation of the view parameters.
   *
   * @return array An array of view parameters
   */
  public function toArray()
  {
    return $this->getAll();
  }

  /**
   * Serializes the current instance.
   *
   * @return array Objects instance
   */
  public function serialize()
  {
    return serialize($this->parameters);
  }

  /**
   * Unserializes a sfParameterHolder instance.
   */
  public function unserialize($serialized)
  {
    $this->parameters = unserialize($serialized);
  }
}
