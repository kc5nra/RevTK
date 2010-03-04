<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<?php include_stylesheets() ?>
<?php include_javascripts() ?>
  <link rel="shortcut icon" href="/css/core/favicon.ico" />
<?php if(has_slot('inline_styles')): ?>
  <style type="text/css">
<?php include_slot('inline_styles') ?>
  </style>
<?php endif ?>
</head>
<?php
  # determine the main top level menu
  $toplevel = $_params->get('module') === 'ui' ? 'ui' : 'main';
  $subtitle = $_params->get('module') === 'ui' ? '.Ui' : '';
?>
<body class="yui-skin-sam">

<!--[if IE]><div id="ie"><![endif]--> 

	<h1><?php echo link_to('Core', '@homepage', array('title'=>'Go to homepage')).$subtitle ?> framework</h1>

	<div id="body">
	  <div class="padding">
<?php echo $core_content ?>
	  </div>
	  <div id="footer">
		  <p>Page generated in <span style="color:#ccc"><?php echo coreContext::getInstance()->getConfiguration()->timeEnd() ?></span> secs</p>
	  </div>
	</div>

	<div id="leftcolumn">
	  <?php if (has_slot('sidebar')): ?>
      <?php include_slot('sidebar') ?>
    <?php else: ?>
      <!-- default sidebar code -->
      <?php include_partial('global/mainMenu') ?>
    <?php endif; ?>
	</div>
	<div class="clear"></div>

<!--[if IE]></div><![endif]-->

</body>
</html>
