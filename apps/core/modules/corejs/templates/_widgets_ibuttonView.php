<?php use_helper('Widgets') ?>

<h2>Helper</h2>

<?php pre_start('code') ?>
// method
string ui_ibtn($label, $internal_uri, $options);
<?php pre_end() ?>

<h2>Examples</h2>

<p>Default button: <?php echo ui_ibtn('Go') ?></p>

<?php pre_start() ?>
echo ui_ibtn('Go');
<?php pre_end() ?>

<p>Disabled button: <?php echo ui_ibtn('Disabled', '#', array('type' => 'uiIBtnDisabled')) ?></p>

<?php pre_start() ?>
echo ui_ibtn('Disabled button', '', array('type' => 'uiIBtnDisabled'));
<?php pre_end() ?>

<p>Centered buttons</p>
  
<div style="text-align:center; background:#eee;padding:10px;">
  <?php echo ui_ibtn('Default button') ?> <?php echo ui_ibtn('Lorem') ?> <?php echo ui_ibtn('Ipsum') ?>
</div>
