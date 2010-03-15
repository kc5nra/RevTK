<h2>View Configuration</h2>

<p> The view configuration settings can be configured at three different levels, each level of customization
    overwrites the corresponding settings at the previous level:
<ul>
  <li>Application level settings : <samp>default_view_configuration</samp> in <b>settings.php</b>
  <li>Module level settings : <samp>all</samp> in <b>modules/[module]/config/view.config.php</b>
  <li>View level settings : <samp>[viewName]</samp> in <b>modules/[module]/config/view.config.php</b>
  <li>Dynamic settings : configuration at run time from the action and view template code
</ul>


<h2>Application View Configuration</h2>

<p> All views get default settings first at the application level, in <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?>.

<p> View configuration settings and corresponding Response methods (from the action):

<ul>
  <li><b>title</b> : $this->getResponse()->setTitle(...)
  <li><b>http_metas</b> [array] : $this->getResponse()->addHttpMeta()
  <li><b>metas</b> [array] : $this->getResponse()->addMeta()
  <li><b>stylesheets</b> [array] : $this->getResponse()->addStylesheet()
  <li><b>javascripts</b> [array] : $this->getResponse()->addJavascript()
</ul>

<p> An example view configuration in settings.php :

<?php pre_start('info') ?>
/**
 * Application-level view configuration.
 *
 */
'default_view_configuration' => array
(
  // The template to use for layout (apps/templates/...)
  'layout'          => 'layout',
  'layout'          => false,  // turn off the layout for Ajax,RSS,etc

  // Default page title
  'title'           => 'App default page title',

  // Http meta tags
  'http_metas' => array
  (
    'cache-control' => 'poo'
  )

  // An array of meta tags
  'metas' => array
  (
    // The charset is set from coreProjectConfiguration::setSymfonyConfig(), don't include it here
    // NOTE Technically, this doesn't work, content-type defaults to "text/html" and
    // can only be overwritten in the action code (fixme?)
    'content-type'     => 'text/html',
    
    'content-language' => 'en',
    'description'      => 'Core project description',
    'keywords'         => 'core, php, framework',
    'copyright'        => '(c) 2005 - 2008 John Doe',
  ),

  // Stylesheets accept an associative array with path => options to set as attributes on the link tag
  // The option 'position' is handled as a parameter for addStylesheet()
  'stylesheets' => array
  (
    '/css/core/core.css',
    
    // Add a media attribute
    '/css/print.css' => array('media' => 'print'),
    
    // Use position 'first' or 'last' to control order of inclusion in the document head
    '/css/include_this_first.css' => array('position' => 'first'),
    '/css/include_this_last.css' => array('position' => 'last')
  ),

  // Javascripts can take extra attributes, and also support the 'position' option
  'javascripts' => array
  (
    '/js/lib/yui/2.7.0/yahoo-dom-event.js',
    
    // Similar to stylesheets, control order with position 'first' or 'last'
    '/js/include_me_first.js' => array('position' => 'first')
  )
);
<?php pre_end() ?>

<h2>Action View Configuration</h2>

<p> View settings can be further specified at the module level, if <var>/config/view.config.php</var> under
    the module directory.
  
<p> View settings are grouped under a unique key which is the name of the view.
    A view name is composed of an action name and an action termination, for example if the <em>index</em>
  action returns coreView::ERROR, the view name will be <em>indexError</em>.

<p> Default view settings can be set at the module level under the <b>all</b> key.

<?php pre_start() ?>
&lt;?php

/**
 * View settings for all views of this module.
 * 
 * The key for each view is "<em>[action name][view name]</em>"
 *
 * The view name by default is not specified if the acton returns coreView::SUCCESS;
 */

return array
(
    // View settings for the 'index' action
    <em>'index'</em> => array
    (
        'title'            => 'Home | Core framework',
        'metas'            => array(
            'description'  => 'Description set through view config file for action "index"'
        )
    ),

    // View settings for the 'index' action, 'Error' view
    <em>'indexError'</em> => array
    (
        <em>...</em>
    ),
    
    // Default settings for all views in this module!
    <em>'all'</em> => array
    (
        <em>...</em>
    )
);
<?php pre_end() ?>

<h2>Dynamic View Configuration</h2>

<p> Lastly, the application and module level configuration can be overwritten by code.

<p> Setting view configuration in the <b>action</b>:

<?php pre_start() ?>
  // Dynamically set page headers in action
  $response = $this->context->getResponse();
  $response->addMeta('robots', 'NONE');
  $response->addMeta('keywords', 'foo bar');
  $response->setTitle('My FooBar Page');
  $response->addStyleSheet('foobar_styles.css', 'last');
  $response->addJavaScript('foobarnav.js');
  
  // In the view template use <em>$_response</em>
  <em>$_response</em>->addJavaScript('ad_banner.js', 'last');
  <em>$_response</em>->addStyleSheet('ad_banner.css', 'last');
<?php pre_end() ?>
