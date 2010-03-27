<?php use_helper('Widgets') ?>

<h2>Helper</h2>

<p>Helper signature:</p>

<?php pre_start('code') ?>
ui_box_rounded($box_class = 'uiBoxRDefault');
string end_ui_box()
<?php pre_end() ?>

<p>Usage</p>

<?php pre_start('html') ?>
ui_box_rounded('uiBoxCustom')
  <p>...html content...</p>
echo end_ui_box()
<?php pre_end() ?>

<h2>Examples</h2>

<?php ui_box_rounded() ?>
  This is the box's contents
<?php echo end_ui_box() ?> 
