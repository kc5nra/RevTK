<div class="layout-home">
	
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
	<div class="col-box col-box-top block">

		<div class="app-header">
			<h2>Register an account</h2>
			<div class="clear"></div>
		</div>

		<p>	Reviewing the Kanji's service is free. Registering an account is necessary for the purpose of managing your flash cards and storing your mnemonics in the Study area. 
		</p>

		<?php echo form_errors() ?>
		
		<?php echo form_tag('account/create', array('class'=>'block')) ?>

		<ul>
		<li><span class="lbl"><?php echo label_for('username', 'Username') ?><span class="req">*</span></span>
			<span class="fld medium"><?php echo input_tag('username', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"><?php echo label_for('email', 'Email') ?><span class="req">*</span></span>
			<span class="fld long"><?php echo input_tag('email', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"><?php echo label_for('password', 'Password') ?><span class="req">*</span></span>
			<span class="fld medium"><?php echo input_password_tag('password', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"><?php echo label_for('password2', 'Confirm password') ?><span class="req">*</span></span>
			<span class="fld medium"><?php echo input_password_tag('password2', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"><?php echo label_for('location', 'Where do you live?') ?></span>
			<span class="fld medium"><?php echo input_tag('location', '', array('class' => 'textfield')) ?>
			<span class="legend">Ex. "Japan" or "Tokyo Japan"</span>
			</span>
		</li>
		<li><span class="lbl"></span>
			<span class="btn"><?php echo submit_tag('Create Account') ?></span>
		</li>
		</ul>
		</form>

		<p class="small">
			<span class="required-legend">*</span>Required fields.<br />
			<span class="required-legend">*</span><strong>Username and password</strong> : please note that you will also be
			automatically registered on the <a href="<?php echo coreConfig::get('app_forum_url') ?>">RtK forums</a> with the same username and password.<br />
			<span class="required-legend">*</span><strong>Email</strong> : please enter a valid e-mail address. Without it, you will
			not be able to retrieve your password should you forget it!
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
