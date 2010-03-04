<?php $_response->setTitle('coreAction - '.$_response->getTitle()) ?>

<h2>coreAction</h2>

<p> The actions contain all the application's logic.
    They use the model and define variables for the view.
    When you make a web request in the application, the URL defines an action and the <?php echo link_to('request', 'doc/core?include_name=request') ?> parameters.

<p> coreAction extends <?php echo link_to('coreComponent', 'doc/core?include_name=component') ?>:

<?php pre_start() ?>
// Forwards current action to the default 404 error action.
function forward404($message = null)
// Forwards to the default 404 error action if the specified condition is true.
function forward404If($condition, $message = null)
// Forwards to the default 404 error action unless the specified condition is true.
function forward404Unless($condition, $message = null)

// Forwards current action to a new one (without browser redirection).
function forward($module, $action)
// If the condition is true, forwards current action to a new one (without browser redirection).
function forwardIf($condition, $module, $action)
// Unless the condition is true, forwards current action to a new one (without browser redirection).
function forwardUnless($condition, $module, $action)

// Redirects current request to a new URL.
// Accepts a full URL, or an internal URL (url_for() format): module/action
function redirect($url, $statusCode = 302)
// Redirects current request to a new URL, only if specified condition is true.
function redirectIf($condition, $url)
// Redirects current request to a new URL, unless specified condition is true.
function redirectUnless($condition, $url)

// Appends the given text to the response content.
// @return coreView::NONE
function renderText($text)
// Appends the result of the given partial execution to the response content.
// @return coreView::NONE
function renderPartial($templateName, $vars = null)
// Appends the result of the given component execution to the response content.
// @return coreView::NONE
function renderComponent($moduleName, $componentName, $vars = null)

// Change the decorator layout for this action's view
// To de-activate the layout, set the layout name to <em>false</em>.
function setLayout($name)
<?php pre_end() ?>

<h2>Actions Class</h2>

<p> Actions are methods named <samp>execute<var>ActionName</var></samp> of a class named <samp><var>moduleName</var>Actions</samp>
	inheriting from the <b>coreActions</b> class (note the "s").
    Actions are stored in an <samp>actions.php</samp> file, in the module's <b>actions/</b> directory.

<p> <b>Actions should often be kept short (not more than a few lines), and all the business logic should usually be in the model.</b>

<p> Sample Action class, with a couple actions named <var>index</var> and <var>list</var> for the <var>mymodule</var> module:
	
<?php pre_start() ?>
// actions.php
class <var>mymodule</var>Actions extends coreActions
{
  // http://localhost/index.php/<var>mymodule</var>/<var>index</var> will request this action
  public function execute<var>Index</var>($request)
  {
    // ...
  }

  // http://localhost/index.php/<var>mymodule</var>/<var>list</var> will request this action
  public function executeList($request)
  {
    // ...
  }
}
<?php pre_end() ?>

<h2>One file per Action</h2>

<p> In this alternative action syntax the class name is <samp><var>actionName</var>Action</samp>, 
    the filename is <samp><var>actionName</var>Action.php</samp>. The class extends <b>coreAction</b>,
    and the method is simply named <samp>execute</samp>:

<?php pre_start() ?>
// <var>login</var>Action.php
class <var>login</var>Action extends coreAction
{
  public function execute($request)
  {
    // ...
  }
}
<?php pre_end() ?>

<h2>Retrieving Information in the Action</h2>

<p> All <?php echo link_to('coreComponent', 'doc/core?include_name=component') ?> methods are available.

<p> Even more objects can be retrieved through the <?php echo link_to('coreContext', 'doc/core?include_name=context') ?> singleton.

<?php pre_start() ?>
class <var>mymodule</var>Actions extends coreActions
{
  public function execute<var>Index</var>($request)
  {
    // Retrieving request parameters
    $password    = $request->getParameter('password');
 
    // Retrieving controller information
    $moduleName  = $this->getModuleName();
    $actionName  = $this->getActionName();
 
    // Retrieving framework core objects
    $userSession = $this->getUser();
    $response    = $this->getResponse();
    $controller  = $this->getController();
    $context     = $this->getContext();
	
    // Note even more is available through the context singleton
    $this->getContext()->getDatabase();
 
    // Setting action variables to pass information to the template
    $this->setVar('foo', 'bar');
    $this->foo = 'bar';            // Shorter version
  }
}
<?php pre_end() ?>

<h2>Action Termination</h2>

<p> The value returned by the action method determines how the view will be rendered.
    Constants of the <b>coreView</b> class are used to specify which template is to be used to display the result of the action.

<p> Call the default view (most common case), using a template file named <b><var>actionName</var>View.php</b>:

<?php pre_start() ?>
  return coreView::SUCCESS;
<?php pre_end() ?>

<p> If there is an error view to call, the action should return <samp>coreView::ERROR</samp>.
    This will load a template file named <b><var>actionName</var>ErrorView.php</b>.

<?php pre_start() ?>
  return coreView::ERROR;
<?php pre_end() ?>

<p> To call a custom view, use this ending, which will look for the view template called <b><var>actionNameMyResult</var>View.php</b>:

<?php pre_start() ?>
  return 'MyResult';
<?php pre_end() ?>

<p> To bypass the view altogether, for example for a batch process:

<?php pre_start() ?>
  return coreView::NONE;
<?php pre_end() ?>

<h2>Bypassing the View</h2>

<p> Bypass the View by setting the Response content and returning <samp>coreView::NONE</samp>:

<?php pre_start() ?>
public function executeIndex()
{
  $this->getResponse()->setContent("<html><body>Hello, World!</body></html>");
 
  return coreView::NONE;
}
<?php pre_end() ?>

<p> An easier way to do this is to use the <b>renderText()</b> method.
    This method <b>adds to</b> the content, so it can be called multiple times.
	It can be used directly as the return value, as in this example:

<?php pre_start() ?>
public function executeIndex()
{
  // renderText returns coreView::NONE
  return $this->renderText("<html><body>Hello, World!</body></html>");
}
<?php pre_end() ?>

<h2>Returning JSON responses</h2>

<p> We can return an object converted to JSON with <?php echo link_to('coreJson', 'doc/core?include_name=json') ?> and
    <b>renderText</b>. Optionally the "Content-Type" header can be set to "application/json" if the front end library
		relies on it.</p>

<?php pre_start() ?>
public function executeJson()
{
  // Set the Content-Type header (optional)
  $this->getResponse()->setHttpHeader('Content-Type', 'application/json');
  // Create an object, or an array (converts to object)
  $o = array('foo' => 'bar');
  // Return stringified JSON as text
  return $this->renderText(coreJson::encode($o));
}
<?php pre_end() ?>


<p> With HTTP Headers only, for example for X-JSON, don't output any content and skip the view:

<?php pre_start() ?>
public function executeHeadersonly()
{
  // Syntax could be wrong, but you get the idea...
  $output = '{"title","My basic letter", "name": "Mr Brown"}';
  $this->getResponse()->setHttpHeader("X-JSON", '('.$output.')');

  return coreView::NONE;
}
<?php pre_end() ?>

<h2>Returning a Partial or a Component</h2>

<p> Instead of resulting in a template, an action can return a partial or a component.
    Use the <b>renderPartial()</b> and <b>renderComponent()</b> methods of the action class.

<p> The variables defined in the action will be automatically passed to the partial/component,
	unless you define an associative array of variables as a second parameter of the method.
   
<?php pre_start() ?>
public function executeFoo()
{
    // the partial will have access to $foo and $bar
    $this->foo = 1234;
    $this->bar = 4567;
   
    return $this->renderPartial('mymodule/mypartial');
}

public function executeFoo2()
{
    $this->foo = 1234;
	
    // the view template of the component will have access to $foo, as well
    // as any other variables set by the component
    return $this->renderComponent('mymodule', 'mycomponent');
}
<?php pre_end() ?>
	

<h2>Skipping to Another Action</h2>

<p> To forward the request to another action, use <b>forward()</b>:

<?php pre_start() ?>
$this->forward('otherModule', 'index');
<?php pre_end() ?>

<p> To redirect the request to another action, user <b>redirect()</b>:

<?php pre_start() ?>
$this->redirect('otherModule/index');
$this->redirect('http://www.google.com/');
<?php pre_end() ?>

<p> The <b>forward404()</b> method forwards to a "page not found" action. This method is often 
	called when a parameter necessary to the action execution is not present in the request:

<?php pre_start() ?>
public function executeShow($request)
{
  $article = $db->fetchRow('SELECT * FROM articles WHERE id = ?',
                           $request->getParameter('id'));
  if (!$article)
  {
    $this->forward404();
  }
}
<?php pre_end() ?>

<p> Most of the time, an action makes a redirect or a forward after testing something.
    For this purpose, the coreActions class has a few more methods, named 
	<b>forwardIf()</b>, <b>forwardUnless()</b>, <b>forward404If()</b>, <b>forward404Unless()</b>, <b>redirectIf()</b>,
	and <b>redirectUnless()</b>.

<p> These help keep the code short and more readable:

<?php pre_start() ?>
// This action is equivalent to the one shown in the previous code block
public function executeShow($request)
{
  $article = $db->fetchRow('SELECT * FROM articles WHERE id = ?',
                           $request->getParameter('id'));
  $this->forward404If(!$article);
}
 
// So is this one
public function executeShow($request)
{
  $article = $db->fetchRow('SELECT * FROM articles WHERE id = ?',
                           $request->getParameter('id'));
  $this->forward404Unless($article);
}
<?php pre_end() ?>
