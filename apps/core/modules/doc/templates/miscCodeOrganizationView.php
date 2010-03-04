<h2>Root Tree Structure</h2>

<?php pre_start('info') ?>
apps/
  <var>frontend</var>/
  <var>backend</var>/
  demo/
  demo_app/
config/
data/
doc/
lib/
  model/
web/
  css/
  images/
  js/
<?php pre_end() ?>

<ul>
	<li><b>apps</b> : contains one folder for each application of the project (typically frontend and backend)<br/>
    <li><b>apps/demo/</b> : the Core framework documenation application<br/>
	<li><b>apps/demo_app/</b> : an application skeleton to be copied for starting a new application<br/>
	<li><b>data</b> : data files of the project, including SQL files that create tables, database schema..<br/>
	<li><b>doc</b> : stores the project documentation
	<li><b>lib</b> : foreign classes or libraries. Code that needs to be shared among your applications.<br/>
		<b>lib/model/</b> : the model subdirectory stores the object model of the project<br/>
	<li><b>web</b> : the root for the web server. The only files accessible from the Internet are the ones located in this directory.
</ul>

<h2>Application Tree Structure</h2>

<pre class="info">
apps/
  <var>[application name]</var>/
    config/
      settings.php
    lib/
    modules/
    templates/
      layoutView.php
</pre>

<ul>
	<li><b>config</b> : contains the <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?> application-level configuration file<br/>
	<li><b>lib</b> : contains classes and libraries that are specific to the application.<br/>
    <li><b>modules</b> : one folder for each module (usually "default" for homepage index)<br/>
    <li><b>templates</b> : contains 'global' templates, and default layout file
</ul>

<h2>Module Tree Structure</h2>

<?php pre_start('info') ?>
apps/
  <var>[application name]</var>/
    modules/
      <var>[module name]</var>/
          actions/
            actions.php
          config/
            view.config.php
          views/
            indexView.php
<?php pre_end() ?>

<ul>
	<li><b>actions</b> : a single class file <em>actions.php</em> and/or separate action files (eg. <em>myactionAction.php</em>)<br/>
    <li><b>config</b> : the <?php echo link_to('view.config.php', 'doc/misc?page_id=viewconfig') ?> file configures all views for this module
</ul>

<h2>Web Tree Structure</h2>

<p>  This maps to the directory of <em>publicly accessible files</em> on the web host.

<?php pre_start('info') ?>
web/
  css/
  images/
  js/
  uploads/
<?php pre_end() ?>
