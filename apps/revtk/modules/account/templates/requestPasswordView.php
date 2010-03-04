<div class="layout-home">
	
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
	<div class="col-box col-box-top block">

		<h2>Request a New Password</h2>

		<p>	Did you forget your password?
		
		<p> Enter your username and you will receive
			a new password by email.
		</p>
		
		<?php echo form_errors() ?>
		
		<?php echo form_tag('@request_password', array('class'=>'block')) ?>

		<ul>
		<li><span class="lbl"><?php echo label_for('username', 'Username') ?></span>
			<span class="fld medium"><?php echo input_tag('username', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"></span>
			<span class="btn"><?php echo submit_tag('Get a new password') ?></span>
		</li>
		</ul>
		</form>

		<p class="small">
			The new password is randomly generated, and may not be easy to remember.
			If you had difficulty remembering your password, consider using a <strong>pass phrase</strong>
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
