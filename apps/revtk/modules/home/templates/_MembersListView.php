<?php use_helper('Date', 'Form', 'Links', 'Widgets') ?>

<p><strong><?php echo $pager->getNbResults() ?></strong> members have been reviewing in the past 30 days.</p>

<?php echo form_tag('home/memberslisttable') ?>
	<?php echo ui_select_table($table, $pager) ?>
</form>