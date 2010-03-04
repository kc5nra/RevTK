<?php use_helper('Form') ?>

<p> This uiAjaxPanel uses loading indicator <em>and shading layer</em>.<br/>
    Adding 'baz' variable to POST data in listener.
</p>

<?php echo form_tag('corejs/uiajaxpaneldemo2') ?>
	<input type="hidden" name="dummy" value="foo" />
	<?php echo submit_tag('Submit') ?>
</form>

<p><strong><?php echo $_request->getMethodName() ?></strong> request:

<?php pre_start('printr') ?><?php echo print_r($_params, true) ?><?php pre_end() ?>
