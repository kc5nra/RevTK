<h2>coreWebRequest</h2>

<p> The coreWebRequest class manages web requests. It parses input from the request and store them as parameters.

<p> All request parameters have the backslashes stripped off, even if get_magic_quotes_gpc() is on.

<p> Methods of the coreWebRequest object:

<?php pre_start() ?>
// Return values for getMethod()
coreRequest::GET
coreRequest::POST

// Get the request method
// @return coreRequest::GET or coreRequest::POST constants
function getMethod()
// Get the request method as a string (eg. 'POST')
function getMethodName()
// Get the value of a given HTTP header
function getHttpHeader($name, $prefix = 'http')
// Get the value of a named cookie
function getCookie($name, $defaultValue = null)
// Is it an SSL request?
function isSecure()

// Is a parameter present in the request?
function hasParameter($name)
// Get the value of a named parameter
function getParameter($name, $default = null)
// Sets a parameter
function setParameter($name, $value)  
// Retrieves the parameter holder
function getParameterHolder()  

// Retrieves the full URI for the current web request
function getUri()
// Retrieves the path info (eg. '/mymodule/myaction')
function getPathInfo()
// Returns referer (sometimes blocked by proxies)
function getReferer()
// Returns the host name (eg. 'www.mysite.com', or 'localhost')
function getHost()
// Returns the front controller path and name (eg. 'myapp_dev.php')
function getScriptName()

// Handling of Request errors

// Retrieves an error message.
function getError($name)
// Retrieves all errors for this request.
function getErrors()
// Removes an error.
// @return Returns the error message of the removed error
function removeError($name)
// Sets an error message.
function setError($name, $message)
// Sets an array of errors.
function setErrors($errors)
// Checks whether or not any errors exists.
function hasErrors()
// Checks if an error exists for given key.
function hasError($name)
<?php pre_end() ?>

<h2>Accessing the Request Object from an Action</h2>

<p> Actions and Components have a few proxies to access the request methods more quickly:

<?php pre_start() ?>
class mymoduleActions extends coreActions
{
  public function executeIndex()
  {
    // Shortcut for $this->getRequest()->hasParameter('foo');
    $hasFoo = $this->hasRequestParameter('foo');
	
	// Shortcut for $this->getRequest()->getParameter('foo');
    $foo    = $this->getRequestParameter('foo');
  }
}
<?php pre_end() ?>

<h2>Routing Request Parameters</h2>

<p> Note that the URL Routing parameters are added to the Request object, and this includes
    also the <b>module</b> and <b>action</b> parameters:

<?php pre_start() ?>
// If you need the current module/action names,
// remember that they are actual request parameters
$module = $this->getRequestParameter('module');
$action = $this->getRequestParameter('action');
<?php pre_end() ?>

<h2>Getting the Remote IP Address</h2>

<p> Here's how to get the REMOTE_ADDR (and other values) without accessing
    the $_SERVER variable directly:
<?php pre_start() ?>
// get remote IP address (doesn't account for proxies etc)
$pathArray = coreContext::getInstance()->getRequest()->getPathInfoArray();
$remote_addr = $pathArray['REMOTE_ADDR'];
<?php pre_end() ?>


<h2>Examples</h2>

<ul>
	<li><b>getUriPrefix()</b>: <samp><?php echo $_request->getUriPrefix() ?></samp>
	<li><b>isSecure()</b>: <samp><?php echo $_request->isSecure() ? 'true':'false' ?></samp>
	<li><b>getReferer()</b>: <samp><?php echo $_request->getReferer() ?></samp>
	<li><b>getHost()</b>: <samp><?php echo $_request->getHost() ?></samp>
	<li><b>getScriptName()</b>: <samp><?php echo $_request->getScriptName() ?></samp>
	<li><b>getPathInfo()</b>: <samp><?php echo $_request->getPathInfo() ?></samp>
</ul>

<p><b>getPathInfoArray()</b>:
<?php pre_start('printr'); print_r($_request->getPathInfoArray()); pre_end() ?>

