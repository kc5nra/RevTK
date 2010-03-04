<h2>sfParameterHolder</h2>

<p> Base class for managing parameters.

<?php pre_start() ?>
// Clears all parameters
function clear()

// Retrieves a parameter.
function & get($name, $default = null)

// Retrieves an array of parameter names.
function getNames()

// Retrieves an associative array of parameters.
function & getAll()

// Indicates whether or not a parameter exists.
function has($name)

// Remove a parameter.
function remove($name, $default = null)

// Sets a parameter.
function set($name, $value)

// Sets a parameter by reference.
function setByRef($name, & $value)

// Sets an array of parameters.
function add($parameters)

// Sets an array of parameters by reference.
function addByRef(& $parameters)

// Serializes the current instance into byte stream that can be stored anywhere
<em>string</em> function serialize()

// Unserializes a sfParameterHolder instance
function unserialize($serialized)
<?php pre_end() ?>

<p>Adding a Parameter Holder to a Class:

<?php pre_start() ?>
class MyClass
{
  protected $parameterHolder = null;
 
  public function initialize($parameters = array())
  {
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }
 
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }
}
<?php pre_end() ?>
