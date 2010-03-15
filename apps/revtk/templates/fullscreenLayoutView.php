<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<?php include_stylesheets() ?>
<?php include_javascripts() ?>
  <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
<?php if(has_slot('inline_styles')): ?>
  <style type="text/css">
<?php include_slot('inline_styles') ?>
  </style>
<?php endif ?>
  <style type="text/css">
html, body { width:100%; height:100%; overflow:hidden; }
  </style>
</head>
<body id="uiFcFullscreen">

<!--[if IE]>
<div id="ie">
<![endif]--> 

<?php echo $core_content ?>

<!--[if IE]>
</div>
<![endif]--> 

<?php if (coreConfig::get('koohii_build')) { use_helper('__Affiliate'); echo analytics_tracking_code(); } ?>

</body>
</html>
