<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<?php include_stylesheets() ?>
  <style type="text/css">
<?php include_slot('inline_styles') ?>
  </style>
<?php include_javascripts() ?>
  <link rel="shortcut icon" href="/css/core/favicon.ico" />
</head>
<body>

  <h1><?php echo link_to('Core', '@homepage', array('title'=>'Go to homepage')) ?> framework</h1>

  <div id="body">
    <div class="padding" style="margin-left:10px">
<?php echo $core_content ?>
    </div>
  </div>
  
  <div id="footer">
    <p>Page generated in <?php echo demoToolkit::timeEnd() ?> secs</p>
  </div>

</body>
</html>
