<h2>coreController</h2>

<p> Note: except for <b>genUrl()</b> it is recommended to use proxy methods:

<ul>
  <li> <?php echo link_to('Actions','doc/core?include_name=action') ?> and <?php echo link_to('Components','doc/core?include_name=component') ?> proxies: <b>getActionName()</b> and <b>getModuleName()</b>.
  <li> The <?php echo link_to('Context','doc/core?include_name=context') ?> object also provides a proxy: <b>getActionInstance()</b>.
</ul>

<p> Methods of coreWebController:

<?php pre_start() ?>
// Retrieves the executing action's name.
function getActionName()
// Retrieves the executing module's name.
function getModuleName()
// Retrieves the running action instance.
function getActionInstance()

// Generates an URL from an array of parameters or internal URI
function genUrl($parameters = array(), $absolute = false)
<?php pre_end() ?>

<h2>Creating an URL from an Action</h2>

<p> If you need to transform an internal URI into an external URL in an action (just as url_for() does in a template)
  use the <b>genUrl()</b> method:

<?php pre_start() ?>
// Using sfController to Transform an Internal URI
$uri = 'article/read?id=21';
 
$url = $this->getController()->genUrl($uri);
// => /article/21
 
$url = $this->getController()->genUrl($uri, true);
// => http://myapp.example.com/article/21
<?php pre_end() ?>
