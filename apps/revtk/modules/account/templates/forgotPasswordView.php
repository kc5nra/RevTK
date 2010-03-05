<div class="layout-home">
	
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
	<div class="col-box col-box-top block">

		<h2>Forget your password?</h2>

		<p>	Enter your email address below and you will receive new password instructions.</p>
    
		<?php echo form_errors() ?>
		
		<?php echo form_tag('@forgot_password', array('class'=>'block')) ?>

		<ul>
		<li><span class="lbl"><?php echo label_for('email_address', 'Email address') ?></span>
			<span class="fld medium"><?php echo input_tag('email_address', '', array('style' => 'width:200px')) ?></span>
		</li>
		<li><span class="lbl"></span>
			<span class="btn"><?php echo submit_tag('Send me password instructions') ?></span>
		</li>
		</ul>
		</form>

		<p class="small">
			<strong>Tip:</strong>	If you had difficulty remembering your password, consider using a <strong>pass phrase</strong>
			(for example "this ninja ate my password"). A pass phrase is longer to type
			but easier to remember and generally more secure due to its length.
		</p>

	</div>
  </div>
 
</div>

<script type="text/javascript">
dom.addEvent(window,'load',function()
{
	$('username').focus();
});
</script>
