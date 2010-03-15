<h2>Links and the Routing System</h2>

<p> Core framework uses <?php echo link_to('Symfony 1.1\'s routing system', 'http://www.symfony-project.org/book/1_1/09-Links-and-the-Routing-System') ?>.

<p> The routing system does two things:
<ul>
  <li>It interprets the external URL of incoming requests and transforms it into an internal URI, to determine the module/action and the request parameters.</li>
  <li>It formats the internal URIs used in links into external URLs (provided that you use the link helpers).</li>
</ul>

<p>Notes:
<ul>
  <li>The routing system parses the routing rules <b>from top to bottom</b> and stops at the first match. This is why you must add your own rules on top of the default ones.</li>
</ul>


<h2>Internal & External Uri Syntax</h2>

<?php pre_start('info') ?>
// Internal URI syntax
&lt;module&gt;/&lt;action&gt;[?param1=value1][&amp;param2=value2][&amp;param3=value3]...

// Example internal URI, which never appears to the end user
article/permalink?year=2006&amp;subject=finance&amp;title=activity-breakdown

// Example external URL, which appears to the end user
http://www.example.com/articles/finance/2006/activity-breakdown.html
<?php pre_end() ?>


<h2>Default routing rules</h2>

<p>The default routing rules, in <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?>:
<?php pre_start('info') ?>
# Easy access to homepage through named rule @homepage
'homepage' => array(
  'url'    => '/',
  'param'  => array( 'module' => 'default', 'action' => 'index' )
),

# This maps any /something to a module of the same name and index action
'default_index' => array(
  'url'    => '/:module',
  'param'  => array( 'action' => 'index' )
),

'default'  => array(
  'url'    => '/:module/:action/*'
)
<?php pre_end() ?>


<h2>Getting Information about the current Route</h2>

<p> The following methods from <b>sfPatternRouting</b> can be used to deal with routes in actions:

<?php pre_start() ?>
// Gets the internal URI for the current request.
function getCurrentInternalUri($withRouteName = false)
// Gets the current route name.
function getCurrentRouteName()
<?php pre_end() ?>

<p> Using sfRouting to get information about the current route:

<?php pre_start() ?>
// If you require a URL like 'http://myapp.example.com/article/21'
 
$routing = coreContext::getInstance()->getRouting();
 
// Use the following in article/read action
$uri = $routing->getCurrentInternalUri();
// => article/read?id=21
 
$uri = $routing->getCurrentInternalUri(true);
// => @article_by_id?id=21
 
$rule = $routing->getCurrentRouteName();
// => article_by_id
 
// If you just need the current module/action names,
// remember that they are actual request parameters
$module = $request->getParameter('module');
$action = $request->getParameter('action');
<?php pre_end() ?>
