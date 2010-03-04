<h2>coreContext</h2>

<p> The context stores a reference to all the core objects
    and the current configuration; it is accessible from everywhere.

<?php pre_start() ?>
function ::getInstance()
function ::hasInstance()

// Retrieve the currently executing action name for this context.
function getActionName()
// Retrieve the currently executing module name for this context.
function getModuleName()
// Retrieve the current coreAction instance.
function getActionInstance()

// Returns the configuration instance
// @return coreApplicationConfiguration
function getConfiguration()

// @return coreWebResponse
function getResponse()
// @return Symfony's sfPatternRouting instance
function getRouting()
// @return coreUser
function getUser()
// @return coreWebRequest
function getRequest()
// @return coreDatabase
function getDatabase()

// Puts an object in the current context.
function set($name, $object)
// Gets an object from the current context, otherwise throws an exception!
function get($name)
<?php pre_end() ?>

<h2>The Context singleton</h2>

<p> The coreContext singleton can be accessed from anywhere in your application:

<?php pre_start() ?>
// Get the coreContext instance and gain access to the database connection
coreContext::getInstance()->getDatabase()
<?php pre_end() ?>

<h2>The Application Configuration</h2>

<p> The application configuration instance gives access to the running <em>application name</em>
    and the <em>environment name</em>.</p>
		
<p> It also gives access to custom methods and properties of the application configuration
	  object in the <samp>apps/config/config.php</samp> file, which extends <strong>coreApplicationConfiguration</strong>.</p>
		
<?php pre_start() ?>
// Get the application name
$appName = coreContext::getInstance()->getConfiguration()->getApplication();

// Get the environment name
$envName = coreContext::getInstance()->getConfiguration()->getEnvironment();
if ($envName === 'staging')
{
  // do something in staging, but not in production
  // ...
}
<?php pre_end() ?>
