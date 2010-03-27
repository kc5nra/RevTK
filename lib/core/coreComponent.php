<?php
/**
 * When used as part of a WebController response, the component can add its
 * own stylesheets and javascript with the addStyle() and addScript() methods. 
 * 
 * @author     Fabrice Denis
 * @package    Core
 * @subpackage Component
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

abstract class coreComponent
{
  protected
    $moduleName             = '',
    $actionName             = '',
    $context                = null,
    $request                = null,
    $varHolder         = null,
    $renderMode             = null;

    public function __construct($context, $moduleName, $actionName)
  {
    $this->initialize($context, $moduleName, $actionName);
  }
  
  /**
   * Initializes this component.
   *
   * @param sfContext The current application context
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName)
  {
    $this->moduleName             = $moduleName;
    $this->actionName             = $actionName;
    $this->context                = $context;
    $this->request                = $context->getRequest();
    $this->varHolder              = new sfParameterHolder();
  }

  /**
   * Execute any application/business logic for this component.
   * 
   * @param  coreRequest The current coreRequest object
   *
   * @return mixed     A string containing the view name associated with this action
   */
  abstract function execute($request);


  /**
   * Gets the module name associated with this component.
   *
   * @return string A module name
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }
  
  /**
   * Gets the action name associated with this component.
   *
   * @return string An action name
   */
  public function getActionName()
  {
    return $this->actionName;
  }
  
  /**
   * Retrieves the current application context.
   *
   * @return coreContext The current coreContext instance
   */
  public final function getContext()
  {
    return $this->context;
  }

  /**
   * Returns the value of a request parameter.
   *
   * This is a shortcut for :
   *
   *   $this->getRequest()->getParameter($name)
   *
   * @param  string The parameter name
   * @param  mixed  Default value
   *
   * @return string The request parameter value
   */
  public function getRequestParameter($name, $default = null)
  {
    return $this->request->getParameter($name, $default);
  }

  /**
   * Returns true if a request parameter exists.
   *
   * This is a proxy method equivalent to:
   *
   *   $this->getRequest()->getParameterHolder()->has($name)
   *
   * @param  string  The parameter name
   * @return boolean true if the request parameter exists, false otherwise
   */
  public function hasRequestParameter($name)
  {
    return $this->requestParameterHolder->has($name);
  }

  /**
   * Retrieves the current coreRequest object.
   *
   * Equivalent to: $this->getContext()->getRequest()
   *
   * @return coreRequest The current coreRequest implementation instance
   */
  public function getRequest()
  {
    return $this->request;
  }
  
  /**
   * Retrieves the user session object.
   *
   * Equivalent to: $this->getContext()->getUser()
   *
   * @return coreUser The current coreUser implementation instance
   */
  public function getUser()
  {
    return $this->context->getUser();
  }

  /**
   * Retrieves the coreController object.
   *
   * Equivalent to: $this->getContext()->getController()
   *
   * @return coreController The current coreController implementation instance
   */
  public function getController()
  {
    return $this->context->getController();
  }

  /**
   * Returns the response object.
   * 
   * Equivalent to: $this->getContext()->getResponse()
   * 
   * @return 
   */
  public function getResponse()
  {
    return $this->context->getResponse();
  }

  /**
   * Sets a variable for the template.
   *
   * @param string The variable name
   * @param mixed   The variable value
   */
  public function setVar($name, $value)
  {
    $this->varHolder->set($name, $value);
  }

  /**
   * Gets a variable set for the template.
   *
   * @param  string The variable name
   * @return mixed  The variable value
   */
  public function getVar($name)
  {
    return $this->varHolder->get($name);
  }

  /**
   * Gets the sfParameterHolder object that stores the template variables.
   *
   * @return sfParameterHolder The variable holder.
   */
  public function getVarHolder()
  {
    return $this->varHolder;
  }

  /**
   * Sets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->setVar('name', 'value')</code>
   *
   * @param  string The variable name
   * @param  string The variable value
   *
   * @return boolean always true
   *
   * @see setVar()
   */
  public function __set($key, $value)
  {
    return $this->varHolder->setByRef($key, $value);
  }

  /**
   * Gets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVar('name')</code>
   *
   * @param  string The variable name
   *
   * @return mixed The variable value
   *
   * @see getVar()
   */
  public function & __get($key)
  {
    return $this->varHolder->get($key);
  }

  /**
   * Returns true if a variable for the template is set.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVarHolder()->has('name')</code>
   *
   * @param  string The variable name
   *
   * @return boolean true if the variable is set
   */
  public function __isset($name)
  {
    return $this->varHolder->has($name);
  }

  /**
   * Removes a variable for the template.
   *
   * This is just really a shortcut for:
   *
   * <code>$this->getVarHolder()->remove('name')</code>
   *
   * @param  string The variable Name
   */
  public function __unset($name)
  {
    $this->varHolder->remove($name);
  }
}

abstract class coreComponents extends coreComponent
{
  public function execute($request)
  {
    // action not found
    throw new coreException('coreComponents initialization failed.');
  }
}
