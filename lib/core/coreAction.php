<?php
/**
 * 
 * @author    Fabrice Denis
 * @package   Core
 * @copyright Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

abstract class coreAction extends coreComponent
{
  protected
    $security = array();

  /**
   * Initializes this action.
   *
   * @param coreContext The current application context.
   *
   * @return bool true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName)
  {
    parent::initialize($context, $moduleName, $actionName);

    // include security configuration
    $file = coreConfig::get('app_module_dir').'/'.$moduleName.'/config/security.config.php';

    if (is_readable($file))
    {
      $this->security = require($file);

      // php config file must return an array
      if (!is_array($this->security))
      {
        throw new coreException('Security config file for module '.$moduleName.' appears to be invalid.');
      }
    }
  }

  /**
   * Indicates that this action requires security.
   *
   * @return bool true, if this action requires security, otherwise false.
   */
  public function isSecure()
  {
    $actionName = strtolower($this->getActionName());

    if (isset($this->security[$actionName]['is_secure']))
    {
      return $this->security[$actionName]['is_secure'];
    }

    if (isset($this->security['all']['is_secure']))
    {
      return $this->security['all']['is_secure'];
    }

    return false;
  }

  /**
   * Gets credentials the user must have to access this action.
   *
   * @return mixed An array or a string describing the credentials the user must have to access this action
   */
  public function getCredential()
  {
    $actionName = strtolower($this->getActionName());

    if (isset($this->security[$actionName]['credentials']))
    {
      $credentials = $this->security[$actionName]['credentials'];
    }
    else if (isset($this->security['all']['credentials']))
    {
      $credentials = $this->security['all']['credentials'];
    }
    else
    {
      $credentials = null;
    }

    return $credentials;
  }

  /**
   * Forwards current action to the default 404 error action.
   *
   * @param  string Message of the generated exception
   *
   * @throws  coreError404Exception
   *
   */
  public function forward404($message = null)
  {
    throw new coreError404Exception($this->get404Message($message));
  }

  /**
   * Forwards current action to the default 404 error action unless the specified condition is true.
   *
   * @param bool   A condition that evaluates to true or false
   * @param string Message of the generated exception
   *
   * @throws coreError404Exception
   */
  public function forward404Unless($condition, $message = null)
  {
    if (!$condition)
    {
        throw new coreError404Exception($this->get404Message($message));
    }
  }
  
  /**
   * Forwards current action to the default 404 error action if the specified condition is true.
   *
   * @param bool   A condition that evaluates to true or false
   * @param string Message of the generated exception
   *
   * @throws coreError404Exception
   */
  public function forward404If($condition, $message = null)
  {
    if ($condition)
    {
      throw new coreError404Exception($this->get404Message($message));
    }
  }

  /**
   * Forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param string A module name
   * @param string An action name
   *
   * @throws coreStopException
   */
  public function forward($module, $action)
  {
    $this->getController()->forward($module, $action);

    throw new coreStopException();
  }

  /**
   * If the condition is true, forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param bool   A condition that evaluates to true or false
   * @param string A module name
   * @param string An action name
   *
   * @throws coreStopException
   */
  public function forwardIf($condition, $module, $action)
  {
    if ($condition)
    {
      $this->forward($module, $action);
    }
  }

  /**
   * Unless the condition is true, forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param bool   A condition that evaluates to true or false
   * @param string A module name
   * @param string An action name
   *
   * @throws coreStopException
   */
  public function forwardUnless($condition, $module, $action)
  {
    if (!$condition)
    {
      $this->forward($module, $action);
    }
  }

  /**
   * Redirects current request to a new URL.
   *
   * 2 URL formats are accepted :
   *  - a full URL: http://www.google.com/
   *  - an internal URL (url_for() format): module/action
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  string Url
   * @param  string Status code (default to 302)
   *
   * @throws coreStopException
   */
  public function redirect($url, $statusCode = 302)
  {
    $this->getController()->redirect($url, 0, $statusCode);

    throw new coreStopException();
  }

  /**
   * Redirects current request to a new URL, only if specified condition is true.
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   A condition that evaluates to true or false
   * @param  string url
   *
   * @throws coreStopException
   *
   * @see redirect
   */
  public function redirectIf($condition, $url)
  {
    if ($condition)
    {
      $this->redirect($url);
    }
  }

  /**
   * Redirects current request to a new URL, unless specified condition is true.
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   A condition that evaluates to true or false
   * @param  string Url
   *
   * @throws coreStopException
   *
   * @see redirect
   */
  public function redirectUnless($condition, $url)
  {
    if (!$condition)
    {
      $this->redirect($url);
    }
  }

  /**
   * Appends the given text to the response content and bypass the built-in
   * view system if used as a return value for the action.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderText('some text')</code>
   *
   * @param  string Text to append to the response
   *
   * @return coreView::NONE
   */
  public function renderText($text)
  {
    $this->getResponse()->setContent($this->getResponse()->getContent().$text);

    return coreView::NONE;
  }

  /**
   * Returns the partial rendered content.
   *
   * If the vars parameter is omitted, the action's internal variables
   * will be passed, just as it would to a normal template.
   *
   * If the vars parameter is set then only those values are
   * available in the partial.
   *
   * @param  string $templateName partial name
   * @param  array  $vars         vars
   *
   * @return string The partial content
   */
  public function getPartial($templateName, $vars = null)
  {
    coreToolkit::loadHelpers('Partial');

    $vars = !is_null($vars) ? $vars : $this->varHolder->getAll();

    return get_partial($templateName, $vars);
  }

  /**
   * Appends the result of the given partial execution to the response content
   * and bypass the view if used as a return value for the action.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderPartial('foo/bar')</code>
   *
   * @param  string $templateName partial name
   * @param  array  $vars         vars
   *
   * @return  coreView::NONE
   *
   * @see    getPartial
   */
  public function renderPartial($templateName, $vars = null)
  {
    return $this->renderText($this->getPartial($templateName, $vars));
  }

  /**
   * Returns the component rendered content.
   *
   * If the vars parameter is omitted, the action's internal variables
   * will be passed, just as it would to a normal template.
   *
   * If the vars parameter is set then only those values are
   * available in the component.
   *
   * @param  string  $moduleName    module name
   * @param  string  $componentNae  component name
   * @param  array  $vars      vars
   *
   * @return string  The component rendered content
   */
  public function getComponent($moduleName, $componentName, $vars = null)
  {
    coreToolkit::loadHelpers('Partial');

    $vars = !is_null($vars) ? $vars : $this->varHolder->getAll();

    return get_component($moduleName, $componentName, $vars);
  }

  /**
   * Appends the result of the given component execution to the response content
   * and bypass the view if used as a return value for the action.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderComponent('foo', 'bar')</code>
   *
   * @param  string  $moduleName    module name
   * @param  string  $componentNae  component name
   * @param  array   $vars          vars
   *
   * @return  coreView::NONE
   *
   * @see    getComponent
   */
  public function renderComponent($moduleName, $componentName, $vars = null)
  {
    return $this->renderText($this->getComponent($moduleName, $componentName, $vars));
  }

  /**
   * Sets an alternate layout for this action.
   *
   * To de-activate the layout, set the layout name to false.
   *
   * To revert the layout to the one configured in the view.yml, set the template name to null.
   *
   * @param mixed Layout name or false to de-activate the layout
   */
  public function setLayout($name)
  {
    coreConfig::set('core.view.'.$this->getModuleName().'_'.$this->getActionName().'_layout', $name);
  }

  /**
   * Returns a formatted message for a 404 error.
   *
   * @param  string An error message (null by default)
   *
   * @return string The error message or a default one if null
   */
  protected function get404Message($message = null)
  {
    return is_null($message) ? sprintf('This request has been forwarded to a 404 error page by the action "%s/%s".', $this->getModuleName(), $this->getActionName()) : $message;
  }
}
  
abstract class coreActions extends coreAction
{
  /**
   * Dispatches to the action defined by the 'action' parameter of the current request.
   *
   * This method try to execute the executeXYZ() method of the current object where XYZ is the
   * defined action name.
   *
   * @return string    A string containing the view name associated with this action
   * 
   * @see  coreAction
   */
  public function execute($request)
  {
    // dispatch action
    $actionToRun = 'execute'.ucfirst($this->getActionName());
    if (!is_callable(array($this, $actionToRun)))
    {
      // action not found
      throw new coreException(sprintf('coreAction initialization failed for module "%s", action "%s". You must create a "%s" method.', $this->getModuleName(), $this->getActionName(), $actionToRun));
    }
    
    // run action
    return $this->$actionToRun($request);
  }
}
