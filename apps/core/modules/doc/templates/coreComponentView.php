<h2>coreComponent</h2>

<p> A component is a reusable chunk of code and presentation. Use a component instead of a <?php echo link_to('partial', 'doc/helper?helper_name=partial') ?>, 
    when you need to separate the logic from the presentation.

<?php pre_start() ?>
// Initializes this component.
function initialize($context, $moduleName, $actionName)
// Execute any application/business logic for this component.
function execute($request);

// Gets the module name associated with this component.
function getModuleName()
// Gets the action name associated with this component.
function getActionName()

// Retrieves the current application context.
function getContext()
// Retrieves the current coreRequest object (same as $this->getContext()->getRequest())
function getRequest()
// Shortcut for $this->getRequest()->getParameter(...)
function getRequestParameter($name, $default = null)
// Shortcut for $this->getRequest()->getParameterHolder()->has($name)
function hasRequestParameter($name)
// Retrieves the user session object (same as $this->getContext()->getUser())
function getUser()
// Retrieves the coreController object (same as $this->getContext()->getController())
function getController()
// Returns the response object (same as $this->getContext()->getResponse())
function getResponse()

// Sets a variable for the template.
function setVar($name, $value)
// Gets a variable set for the template.
function getVar($name)
// Gets the sfParameterHolder object that stores the template variables.
function getVarHolder()

// Shortcut for $this->setVar('name', 'value')
function __set($key, $value)
// Shortcut for $this->getVar('name')
function & __get($key)
// Shortcut for $this->getVarHolder()->has('name')
function __isset($name)
// Shortcut for $this->getVarHolder()->remove('name')
function __unset($name)
<?php pre_end() ?>

<h2>The Component Class</h2>

<p> The component's logic is stored in a method named <samp>execute<var>ComponentName</var></samp> of a class named <samp><var>moduleName</var>Components</samp> inheriting from the coreComponents class.
    Components are stored in a <b>components.php</b> file, in the module's <b>actions/</b> directory.

<p> Here's a call to a sample component named <var>headlines</var>, in the <var>news</var> module:

<?php pre_start() ?>
// Call to the component
&lt;?php include_component('news', 'headlines', array('foo' => 'bar')) ?>
<?php pre_end() ?> 

<p> The Components Class, in <samp>modules/<var>news</var>/actions/components.php</samp>:

<?php pre_start() ?>
class <var>news</var>Components extends coreComponents
{
  public function execute<var>Headlines</var>()
  {
    // Access a variable that was passed to the component
    echo $this->foo;   // => "bar"
  
    $this->message = "Hello World!";
  }

}
<?php pre_end() ?>

<p> The component template, in <samp>modules/<var>news</var>/templates/<var>headlines</var>View.php

<?php pre_start('info') ?>
&lt;h1>Headlines&lt;/h1>
Foo is: &lt;?php echo $foo ?>&lt;br/>
Messages is: &lt;?php echo $message ?>
<?php pre_end() ?>


<h2>Component Stored in a Separate File</h2>

<p> Note in this alternative action syntax the class name is <samp><var>componentName</var>Component</samp>, extends <b>coreComponent</b> (not plural),
    and the method is simply named <samp>execute</samp>:

<?php pre_start() ?>
class <var>headlines</var>Component extends coreComponent
{
  public function execute($request)
  {
    // ...
  }
}
<?php pre_end() ?>
