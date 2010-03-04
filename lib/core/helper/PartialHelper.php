<?php
/**
 * Helpers for including partials and components in templates.
 * 
 * @package    Core
 * @subpackage helper
 * @author     Fabrice Denis
 * @copyright  Based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

/**
 * Evaluates and echoes a partial.
 * The partial name is composed as follows: 'mymodule/mypartial'.
 * The partial file name is mypartialView.php and is looked for in modules/mymodule/templates/.
 * If the partial name doesn't include a module name,
 * then the partial file is searched for in the caller's templates/ directory.
 * If the module name is 'global', then the partial file is looked for in <myapp>/templates/.
 * For a variable to be accessible to the partial, it has to be passed in the second argument.
 *
 * Example:
 *   include_partial('mypartial', array('myvar' => 12345));
 *
 * @param  string partial name
 * @param  array variables to be made accessible to the partial
 *
 * @see    get_partial, include_component
 */
function include_partial($templateName, $vars = array())
{
	echo get_partial($templateName, $vars);
}

/**
 * Evaluates and returns a partial.
 * The syntax is similar to the one of include_partial
 *
 * Example:
 *   echo get_partial('mypartial', array('myvar' => 12345));
 *
 * @param  string partial name
 * @param  array variables to be made accessible to the partial
 * @return string result of the partial execution
 * @see    include_partial
 */
function get_partial($templateName, $vars = array())
{
	$context = coreContext::getInstance();

	// partial is in another module?
	if (false !== $sep = strpos($templateName, '/'))
	{
		$moduleName   = substr($templateName, 0, $sep);
		$templateName = substr($templateName, $sep + 1);
	}
	else
	{
		$moduleName = $context->getController()->getModuleName();
	}
	$actionName = '_'.$templateName;
	
	$view = new coreView($context, $moduleName, $actionName, '');

    // pass attributes to the view and render
    $view->getParameterHolder()->add($vars);

	return $view->render();
}

/**
 * Evaluates and echoes a component.
 * For a variable to be accessible to the component and its partial, 
 * it has to be passed in the third argument.
 *
 * <b>Example:</b>
 * <code>
 *  include_component('mymodule', 'mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string module name
 * @param  string component name
 * @param  array variables to be made accessible to the component
 *
 * @see    get_component, include_partial, include_component_slot
 */
function include_component($moduleName, $componentName, $vars = array())
{
	echo get_component($moduleName, $componentName, $vars);
}

/**
 * Evaluates and returns a component.
 * The syntax is similar to the one of include_component.
 *
 * <b>Example:</b>
 * <code>
 *  echo get_component('mymodule', 'mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string module name
 * @param  string component name
 * @param  array variables to be made accessible to the component
 * @return string result of the component execution
 * @see    include_component
 */
function get_component($moduleName, $componentName, $vars = array())
{
	$context = coreContext::getInstance();
	$actionName = '_'.$componentName;

	$allVars = _call_component($moduleName, $componentName, $vars);

	if (!is_null($allVars))
	{
		// render
		$view = new coreView($context, $moduleName, $actionName, '');
//    	$view->getParameterHolder()->add($vars);
		$view->getParameterHolder()->add($allVars);
		
		return $view->render();
	}
}

/**
 * Begins the capturing of the slot.
 *
 * @param	string $name	 slot name
 * @param	string $value	The slot content
 *
 * @see		end_slot
 */
function slot($name, $value = null)
{
	$context = coreContext::getInstance();
	$response = $context->getResponse();

	$slot_names = coreConfig::get('core.view.slot_names', array());
	if (in_array($name, $slot_names))
	{
		throw new coreException(sprintf('A slot named "%s" is already started.', $name));
	}

	if (!is_null($value))
	{
		$response->setSlot($name, $value);

		return;
	}

	$slot_names[] = $name;

	$response->setSlot($name, '');
	coreConfig::set('core.view.slot_names', $slot_names);

	ob_start();
	ob_implicit_flush(0);
}

/**
 * Stops the content capture and save the content in the slot.
 *
 * @see		slot
 */
function end_slot()
{
	$content = ob_get_clean();

	$response = coreContext::getInstance()->getResponse();
	$slot_names = coreConfig::get('core.view.slot_names', array());
	if (!$slot_names)
	{
		throw new coreException('No slot started.');
	}

	$name = array_pop($slot_names);

	$response->setSlot($name, $content);
	coreConfig::set('core.view.slot_names', $slot_names);
}

/**
 * Returns true if the slot exists.
 *
 * @param	string $name	slot name
 *
 * @return bool true, if the slot exists
 * @see		get_slot, include_slot
 */
function has_slot($name)
{
	return array_key_exists($name, coreContext::getInstance()->getResponse()->getSlots());
}

/**
 * Evaluates and echoes a slot.
 *
 * <b>Example:</b>
 * <code>
 *	include_slot('navigation');
 * </code>
 *
 * @param	string $name	slot name
 *
 * @see		has_slot, get_slot
 */
function include_slot($name)
{
	return ($v = get_slot($name)) ? print $v : false;
}

/**
 * Evaluates and returns a slot.
 *
 * <b>Example:</b>
 * <code>
 *	echo get_slot('navigation');
 * </code>
 *
 * @param	string $name	slot name
 *
 * @return string content of the slot
 * @see		has_slot, include_slot
 */
function get_slot($name)
{
	$context = coreContext::getInstance();
	$slots = $context->getResponse()->getSlots();

	return isset($slots[$name]) ? $slots[$name] : '';
}

function _call_component($moduleName, $componentName, $vars)
{
	$context = coreContext::getInstance();

	$controller = $context->getController();

	if (!$controller->componentExists($moduleName, $componentName))
	{
		// cannot find component
		throw new coreException(sprintf('The component does not exist: "%s", "%s".', $moduleName, $componentName));
	}

	// create an instance of the action
	$componentInstance = $controller->getComponent($moduleName, $componentName);

	$componentInstance->getVarHolder()->add($vars);

	// dispatch component
	$componentToRun = 'execute'.ucfirst($componentName);
	if (!method_exists($componentInstance, $componentToRun))
	{
		if (!method_exists($componentInstance, 'execute'))
		{
			// component not found
			throw new coreException(sprintf('coreComponent initialization failed for module "%s", component "%s".', $moduleName, $componentName));
		}

		$componentToRun = 'execute';
	}

	// run component

	$retval = $componentInstance->$componentToRun($context->getRequest());

	return coreView::NONE == $retval ? null : $componentInstance->getVarHolder()->getAll();
}
