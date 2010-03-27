<p>AjaxTable uses Core.Ui.AjaxPanel to allow sorting and paging using ajax updates.</p>

<div id="demo">
  <?php include_partial('ajaxtable') ?>
</div>

<h2>How it works</h2>

<p>Include the php table component as a component in the view template:</p>

<?php pre_start('code') ?>
&lt;div id="MembersListComponent">
  &lt;?php include_component('home', 'MembersList') ?&gt;
&lt;/div>
<?php pre_end() ?>

<p>Instance the AjaxTable, passing the container element</p>

<?php pre_start('js') ?>
Core.ready(function() {
  var ajaxTable = new Core.Ui.AjaxTable(Core.Ui.get('MembersListComponent'));
});
<?php pre_end() ?>

<p>The component's view returns a FORM element and the table code. The FORM element
   is output by the form_tag() helper (FormHelper). The form's action attribute 
   (the ajax url) and the method (post) will be used by AjaxPanel for the request.</p>
   
<?php pre_start('code') ?>
&lt;?php use_helper('Form') ?>

&lt;?php echo form_tag('corejs/ajaxtable') ?>
  &lt;?php echo input_hidden_tag('hidden1', 'foobar') ?>
&lt;/form>

&lt;table cellspacing="0" class="tabular">
  ...
&lt;/table>
<?php pre_end() ?>

<script type="text/javascript">
Core.ready(function() {
  var ajaxTable = new Core.Ui.AjaxTable(Core.Ui.get('demo'));
});
</script>
