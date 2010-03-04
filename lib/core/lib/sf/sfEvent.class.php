<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfEvent.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfEvent.class.php 6490 2007-12-13 21:44:39Z fabien $
 */
class sfEvent implements ArrayAccess
{
  protected
    $value      = null,
    $processed  = false,
    $subject    = null,
    $name       = '',
    $parameters = null;

  /**
   * Constructs a new sfEvent.
   *
   * @param mixed  The subject
   * @param string The event name
   * @param array  An array of parameters
   */
  public function __construct($subject, $name, $parameters = array())
  {
    $this->subject = $subject;
    $this->name = $name;

    $this->parameters = $parameters;
  }

  /**
   * Returns the subject.
   *
   * @param mixed The subject
   */
  public function getSubject()
  {
    return $this->subject;
  }

  /**
   * Returns the event name.
   *
   * @param string The event name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Sets the return value for this event.
   *
   * @param mixed The return value
   */
  public function setReturnValue($value)
  {
    $this->value = $value;
  }

  /**
   * Returns the return value.
   *
   * @return mixed The return value
   */
  public function getReturnValue()
  {
    return $this->value;
  }

  /**
   * Sets the processed flag.
   *
   * @param Boolean The processed flag value
   */
  public function setProcessed($processed)
  {
    $this->processed = (boolean) $processed;
  }

  /**
   * Returns whether the event has been processed by a listener or not.
   *
   * @param Boolean true if the event has been processed, false otherwise
   */
  public function isProcessed()
  {
    return $this->processed;
  }

  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * Returns true if the parameter exists (implements the ArrayAccess interface).
   *
   * @param  string  The parameter name
   *
   * @return Boolean true if the parameter exists, false otherwise
   */
  public function offsetExists($name)
  {
    return isset($this->parameters[$name]);
  }

  /**
   * Returns a parameter value (implements the ArrayAccess interface).
   *
   * @param  string The parameter name
   *
   * @return mixed  The parameter value
   */
  public function offsetGet($name)
  {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
  }

  /**
   * Sets a parameter (implements the ArrayAccess interface).
   *
   * @param string The parameter name
   * @param mixed  
   */
  public function offsetSet($name, $value)
  {
    $this->parameters[$name] = $value;
  }

  /**
   * Removes a parameter (implements the ArrayAccess interface).
   *
   * @param string The parameter name
   */
  public function offsetUnset($name)
  {
    unset($this->parameters[$name]);
  }
}
