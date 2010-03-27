<h2>Parameter Holders</h2>

<p> Classes that contain a <?php echo link_to('parameter holder', 'doc/lib?page_id=sfParameterHolder') ?> often provide proxy methods to shorten
    the code needed for get/set operations:

<?php pre_start() ?>
  // Access coreRequest's parameter holder
  $request->getParameterHolder()->set('foo', 'bar');
  <em>echo</em> $request->getParameterHolder()->get('foo');

  // Using the more concise proxy methods
  $request->setParameter('foo', 'bar');
  <em>echo</em> $request->getParameter('foo');
<?php pre_end() ?>

<p> The proxy getter accepts a default value, which simplifies code:
<?php pre_start() ?>
  // A default value can be used by putting the getter in a condition
  if ($request->hasParameter('foobar'))
  {
    <em>echo</em> $request->getParameter('foobar');
  }
  else
  {
    <em>echo</em> 'default';
  }

  // Much faster way using the second getter argument
  <em>echo</em> $request->getParameter('foobar', 'default');
<?php pre_end() ?>


<h2>Class Autoloading</h2>

<p> coreAutoload attempts to load files in this order :

<ul>
  <li>If the filename starts with <var>core</var> it looks in the <samp>lib/core</samp> directory.
  <li>If the filename matches <var>sf[A-Z]</var> it looks in the <samp>lib/core/lib/sf/</samp> directory.
  <li>If it is defined in settings.php 'autoload_classes', then it looks in the defined directory.
  <li>If the filename ends in <var>Peer</var>, coreAutoload looks for a data model first in
      the application's model directory <samp>apps/[myapp]/lib/model/</samp> and then in the project
    model directory <samp>lib/model/</samp>.
</ul>

<h2>Integrating with Other Framework Components</h2>

<p> Extending the autoloading system to enable third party components
    is done simply by registering the autoload classes of those components
  in the application configuration class.
  
<p> The callback methods registered by spl_autoload_register() calls will
  be called one after the other in the same order that they were registered.
  
<p> For example to enable autoloading of Zend classes:

<?php pre_start() ?>
// Requires a 'app_zend_lib_dir' setting in settings.php

// Integrate Zend from project level directory /lib/Zend/
if ($zend_lib_dir = coreConfig::get('app_zend_lib_dir'))
{
  set_include_path($zend_lib_dir.PATH_SEPARATOR.get_include_path());
  require_once($zend_lib_dir.'/Zend/Loader.php');
  spl_autoload_register(array('Zend_Loader', 'autoload'));
}
<?php pre_end() ?>


<h2>Batch Mode</h2>

<p> In batch mode, you use the front controller and simply skip the last line which
    calls the dispatch() method on the controller.

<p> The <b>CORE_APP</b> constant should designate which existing application
    from which the settings should be used, as this determines database connection
  parameters etc.

<?php pre_start() ?>
&lt;?php
// Front controller for batch file execution
define('CORE_ROOT_DIR', realpath(dirname(__FILE__).'/..'));
define('CORE_APP',      '<em>MyApp</em>');
define('CORE_DEBUG',    true);

require_once(CORE_ROOT_DIR.'/lib/core/core.php');
$configuration = new coreProjectConfiguration(CORE_ROOT_DIR, CORE_APP, CORE_DEBUG);
coreContext::createInstance($configuration);

// A batch file skips the last line of the front controller:
<strike>//coreContext::getInstance()->getController()->dispatch();</strike>

// The batch code starts here and can access application resources
// through coreContext, for example to access the database:

$db = coreContext::getInstance()->getDatabase();
<?php pre_end() ?>


<h2>Json</h2>

<p> Activate the <?php echo link_to('application/json media type','http://www.ietf.org/rfc/rfc4627.txt') ?> on output:
<?php pre_start() ?>
  <u>// Set HTTP Response content type</u>
  $this->getResponse()->setContentType(<em>'application/json'</em>);
<?php pre_end() ?>
