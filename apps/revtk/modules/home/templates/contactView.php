<div class="layout-home">

<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  	<div class="col-box col-box-top">

		<div class="app-header">
			<h2>Contact</h2>
			<div class="clearboth"></div>
		</div>
		
		<p> Your feedback is very much appreciated and will help me improve the website
			for everyone. Thank you!
		</p>
		<p class="small"><span class="required-legend">*</span>Please provide a valid email address so that I may reply to you.
		</p>

		<?php echo form_errors() ?>

		<div id="feedbackform">
		<?php echo form_tag('home/contact', array('class'=>'block')) ?>
		<ul>
		<li><span class="lbl"><label for="name">Name</label></span>
			<span class="fld medium"><?php echo input_tag('name', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><span class="lbl"><label for="email">Email</label><span class="req">*</span></span>
			<span class="fld medium"><?php echo input_tag('email', '', array('class' => 'textfield')) ?></span>
		</li>
		<li><label for="message">Message</label><br />
			<?php echo textarea_tag('message', '', 'rows=12 cols=55 class=textfield') ?>
		</li>
		<li>
			<?php echo submit_tag('Send') ?>
		</li>
		</ul>
		</form>
		</div>

	</div>
  </div>

</div>

<script type="text/javascript">
dom.addEvent(window,'load',function()
{
	$('name').focus();
});
</script>
