<h2>Default Front Controller</h2>

<p>The default front controller called <b>index.php</b> is located in the web/ directory.

<p>The default production front controller looks like this:

<?php pre_start() ?>
&lt;?php
define('CORE_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('CORE_APP',         'myapp');
define('CORE_ENVIRONMENT', 'prod');
define('CORE_DEBUG',       false);

require_once(CORE_ROOT_DIR.'/apps/'.CORE_APP.'/config/config.php');
$configuration = new myappConfiguration(CORE_ENVIRONMENT, CORE_DEBUG, CORE_ROOT_DIR);

coreContext::createInstance($configuration);
coreContext::getInstance()->getController()->dispatch();
<?php pre_end() ?>


<p>How the bootstrap works:
<ul>
	<li>The front controller is called (/web/index.php, /web/backend.php, etc)
	<li>The front controller defines some constants:<br/>
	    <samp>CORE_ROOT_DIR</samp> : Project root directory.<br/>
		<samp>CORE_APP</samp> : Application name in the project. Necessary to compute file paths.<br/>
		<samp>CORE_ENVIRONMENT</samp> : Determines the application configuration, see
		<?php echo link_to('application settings', 'doc/misc?page_id=settings') ?>.
	<li>The front controller includes /apps/myapp/config/config.php which includes core.php, and creates an instance of coreApplicationConfiguration
	<li>The coreProjectConfiguration instantiation sets up the application configuration and environment
	    (error handling, class autoloading, ...)
	<li>The web front calls ->dispatch() on the controller
</ul>

<h2>Web Server Configuration</h2>

<p> Open the <b>httpd.conf</b> file of your <samp>Apache/conf/</samp> directory and add at the end:

<?php pre_start('info') ?>
NameVirtualHost 127.0.0.1:80

&lt;VirtualHost 127.0.0.1:80>
  ServerName <b>myapp.localhost</b>
  DocumentRoot "D:/Sites/<b>MyApp</b>/web/"
  DirectoryIndex index.php

  &lt;Directory "D:/Sites/<b>MyApp</b>/web/">
   AllowOverride All
   Allow from All
  <&lt;/Directory>
&lt;/VirtualHost>
<?php pre_end() ?>

<p> Declare the domain name in your <samp>C:\WINDOWS\system32\drivers\etc\hosts</samp> file:

<?php pre_start('info') ?>
127.0.0.1         <b>myapp.localhost</b>
<?php pre_end() ?>


<h2>Default .htaccess Configuration</h2>

<?php pre_start('info') ?>
&lt;IfModule mod_rewrite.c>
  RewriteEngine On

  # we skip all files with .something
  RewriteCond %{REQUEST_URI} \..+$
  RewriteCond %{REQUEST_URI} !\.html$
  RewriteRule .* - [L]

  # we check if the .html version is here (caching)
  RewriteRule ^$ index.html [QSA]
  RewriteRule ^([^.]+)$ $1.html [QSA]
  RewriteCond %{REQUEST_FILENAME} !-f

  # no, so we redirect to our front web controller
  RewriteRule ^(.*)$ index.php [QSA,L]
&lt;/IfModule>
<?php pre_end() ?>
