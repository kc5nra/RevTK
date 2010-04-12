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


<h2>Pattern constraints</h2>

<p> When a URL can match more than one rule, you must refine the rules by adding constraints (requirements), to the pattern.
    A <em>requirement</em> is a set of regular expressions that must be matched by the wildcards for the rule to match.</p>

<?php pre_start('info') ?>
  'article_by_id' => array(
    'url'          => '/article/:id',
    'param'        => array( 'module' => 'article', 'action' => 'read' ),
    'requirements' => array( 'id' => '\d+' )
  ),
  'article_by_slug' => array(
    'url'          => '/article/:slug',
    'param'        => array( 'module' => 'article', 'action' => 'permalink' )
  ),
<?php pre_end() ?>

<p> The corresponding template calls: </p>

<?php pre_start() ?>
&lt;?php echo link_to('my article', 'article/permalink?slug='.$article->getSlug()) ?&gt;
<?php pre_end() ?>


<h2>Cookbook</h2>

<p> The requirements parameter can be used to match dots. This avoids a problem where sfRouting::parse() uses dot
    and slash characters by default to split the route parts (cf. "segment_separators" configuration of sfPatternRouting):

<?php pre_start('info') ?>
// This url would cause a dispatch error  
http://www.domain.com/user/john.doe

// The requirements pattern allows the rule to match
'user_profile' => array(
  'url'          => '/user/:name',
  'param'        => array( 'module' => 'user', 'action' => 'profile' ),
  'requirements' => array( 'name' => '[\w\.]+' )
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
