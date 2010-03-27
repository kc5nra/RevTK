<?php use_helper('Widgets') ?>

<h3>Markup reference:</h3>

<?php pre_start('html') ?>
<div class="uiProgressBar">
  <div>
    <span class="g" title="30 of 50" style="width:60%"></span>
    <span class="r" title="5 of 50" style="width:10%"></span>
  </div>
</div>
<?php pre_end() ?>

<h2>ui_progress_bar() helper</h2>

<?php echo ui_progress_bar(array(array('value' => 75), array('value' => 0, 'label' => 'custom label', 'class' => 'r')), 100, array('id' => 'custom_id')) ?>

<p>With custom border color:</p>

<?php echo ui_progress_bar(array(array('value' => 75), array('value' => 0, 'label' => 'custom label', 'class' => 'r')), 100, array('borderColor' => '#A1A1A1')) ?>
