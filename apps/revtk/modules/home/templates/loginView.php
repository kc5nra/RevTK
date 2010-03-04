<?php use_helper('Form', 'Validation') ?>

<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  	<div class="col-box col-box-top">

		<div class="app-header">
			<h2>Sign in</h2>
			<div class="clear"></div>
		</div>

		<?php echo form_errors() ?>

		<?php echo form_tag('home/login', array('class'=>'block')) ?>
		
		<?php echo input_hidden_tag('referer', $_request->getParameter('referer')) ?>

		<ul>
		<li><span class="lbl"><?php echo label_for('username', 'Username') ?><span class="req">*</span></span>
			<span class="fld medium"><?php echo input_tag('username', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"><?php echo label_for('password', 'Password') ?><span class="req">*</span></span>
			<span class="fld medium"><?php echo input_password_tag('password', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"></span>
			<span class="fld"><?php echo checkbox_tag('rememberme', '1', false, array('id' => 'rememberme', 'class' => 'checkbox')) ?><?php echo label_for('rememberme', 'Keep me logged in') ?></span>
		</li>
		<li><span class="lbl"></span>
			<span class="btn"><?php echo submit_tag('Login') ?></span>
		</li>
		</ul>
		</form>

		<p><?php echo link_to('Forgot your password ?','@request_password') ?></p>

	</div>
  </div>

</div>

<script type="text/javascript">
dom.addEvent(window,'load',function()
{
	$('username').focus();
});
</script>
