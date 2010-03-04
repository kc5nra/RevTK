<h2>Form Helpers & Form Validation Demo</h2>

<?php use_helper('Form') ?>

<?php if ($_request->hasErrors()): ?>
<p>The form did not validate and the following error messages were set:</p>
<div class="form-errors">
	<?php 
	foreach($_request->getErrors() as $field => $message)
	{
		echo $message.'<br/>';
	}
	?>
</div>
<?php endif ?>

<?php echo form_tag('test/formdemo') ?>

	<?php echo input_hidden_tag('hidden_data','46') ?>

	<table class="form" cellspacing="0">
		<tr>
			<th><?php echo label_for('firstname', 'Firstname<span>StringValidator</span>') ?></th>
			<td><?php echo input_tag('firstname', 'Al', array('class' => 'textfield')) ?></td>
		</tr>
		<tr>
			<th><?php echo label_for('age', 'Age<span>NumberValidator</span>') ?></th>
			<td><?php echo input_tag('age', 45, array('class' => 'textfield')) ?></teyd>
		</tr>
		<tr>
			<th><?php echo label_for('url', 'Website<span>UrlValidator</span>') ?></th>
			<td><?php echo input_tag('url', 'http://www.homestarrunner.com', array('class' => 'textfield')) ?></td>
		</tr>
		<tr>
			<th><?php echo label_for('email', 'Email<span>EmailValidator</span>') ?></th>
			<td><?php echo input_tag('email', 'terence@southpark.com', array('class' => 'textfield')) ?></td>
		</tr>
		<tr>
			<th><?php echo label_for('textarea', 'Description<span>Textarea</span>') ?></th>
			<td><?php 
				// Test the escaping
				$escape_this = "Escape this!\nBackslash \\ Single quote '\nUtf-8 漢字\n&lt; < &gt; > <b>Bolded?</b>\n";
				echo textarea_tag('textarea', $escape_this, array('class' => 'textfield', 'rows'=>6, 'cols'=>30)) ?></td>
		</tr>
		<tr>
			<th><?php echo label_for('cardtype', 'Card Type<span>Dropdown Select</span>') ?></th>
			<td><?php echo select_tag('dropdown', options_for_select(array(
				  'VI' => 'Visa',
				  'EU' => 'Eurocard',
				  'MC' => 'Mastercard'
				), 'EU'), array('style' => 'width:120px')) ?>
			</td>
		</tr>
		<tr>
			<th><?php echo label_for('payment', 'Payment<span>Multiple Select</span>') ?></th>
			<td><?php echo select_tag('payment', options_for_select(
						array('Visa' => 'Visa', 'Eurocard' => 'Eurocard', 'Mastercard' => 'Mastercard'),
						array('Visa', 'Mastercard')), array('multiple' => true, 'style' => 'width:120px'))
						 ?>
			</td>
		</tr>
		<tr>
			<th><?php echo label_for('password', 'Password') ?></th>
			<td><?php echo input_password_tag('password', 'abc123', array('class' => 'textfield')) ?></teyd>
		</tr>
		<tr>
			<th><?php echo label_for('verifypassword', 'Verify Password<span>CompareValidator</span>') ?></th>
			<td><?php echo input_password_tag('verifypassword', 'abc123', array('class' => 'textfield')) ?></teyd>
		</tr>
		<tr>
			<th><label>myCheck[]<span>Check boxes</span></label></th>
			<td><?php echo checkbox_tag('myCheck[]', 'cheese', true) ?><?php echo label_for('myCheck_cheese', 'Cheese (default)') ?><br />
				<?php echo checkbox_tag('myCheck[]', 'lolcats', false) ?><?php echo label_for('myCheck_lolcats', 'Lolcats') ?>
			</td>
		</tr>
		<tr>
			<th><label>myRadio[]<span>Radio buttons</span></label></th>
			<td><?php echo radiobutton_tag('myRadio[]', 'funk' ) ?><?php echo label_for('myRadio_funk', 'Funk') ?><br />
				<?php echo radiobutton_tag('myRadio[]', 'disco', true ) ?><?php echo label_for('myRadio_disco', 'Disco (default)') ?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo submit_tag('Submit', 'name=submit') ?></td>
		</tr>
	</table>
	
</form>

<h2>Request Parameters</h2>

<?php pre_start('printr') ?><?php echo print_r($_params->getAll(), true) ?><?php pre_end() ?>
