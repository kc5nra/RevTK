<?php use_helper('Form') ?>

<?php echo form_tag('corejs/uiajaxpaneldemo1') ?>

	<input type="hidden" name="dummy" value="foo" />

	<?php if ($_request->hasErrors()): ?>
	<p>Oops! There are some validation errors.</p>
	<?php endif ?>

<?php if ($_params->has('reset')): ?>

	<p>This uiAjaxPanel uses loading indicator, but no shading.

	<p><?php echo input_tag('txtName', 'Default value') ?> <input type="submit" name="commit" value="Submit" class="JsAction-post" />

<?php else: ?>

<?php echo input_hidden_tag('i', 1) ?>
<?php echo input_hidden_tag('love', 1) ?>
<?php echo input_hidden_tag('summer', 1) ?>

	<p><strong>You typed in "<?php echo $_request->getParameter('txtName') ?>".</strong>
	
	<?php echo input_hidden_tag('reset', 1) ?>
	<p><input type="submit" name="commit" value="Go back" class="JsAction-post" />

<?php endif ?>

</form>

<p><strong><?php echo $_request->getMethodName() ?></strong> request:

<?php pre_start('printr') ?><?php echo print_r($_params, true) ?><?php pre_end() ?>
