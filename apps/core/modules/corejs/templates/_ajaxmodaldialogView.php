<?php use_helper('Form') ?>

<div class="dialog-head">
	<h3>Are you sure of Lorem Ipsum ?</h3>
</div>
<div class="dialog-body" style="margin:0;">

	<?php echo form_tag('ui/uiajaxpaneldemo1') ?>
	
		<p>This uiAjaxPanel uses loading indicator, but no shading.
	
		<?php if ($_params->has('txtName')): ?>
		<p> Ye typed this hereth: <strong><?php echo $_params->get('txtName') ?></strong></p>
		<?php endif ?>
	
		<p><?php echo input_tag('txtName', 'Default value') ?> <input type="submit" name="commit" value="Submit" class="JsAction-post" />
	
	</form>

	<p><button class='JsAction-close'>Cancel</button>

</div>
