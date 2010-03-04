<?php use_helper('Form', 'CJK', 'Date', 'Links', 'Widgets') ?>

<?php echo form_tag('study/failedlisttable') ?>
  <?php echo ui_select_table($table, $pager) ?>
</form>
