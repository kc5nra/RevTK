<h2>CoreHelper</h2>

<p> Helpers are PHP functions that return HTML code and can be used in templates.
<p> Sometimes, helpers are just time-savers, packaging code snippets frequently used in templates.

<p>Functions available:
<?php pre_start() ?>
  // Declare a helpers to make its functions available in this template
  use_helper(<em>'Helper_name'</em>);

  // Declare multiple helpers at once
  use_helper(<em>'HelperName1'</em>, <em>'HelperName2'</em>, <em>'HelperName3'</em>)
<?php pre_end() ?>

<h2>Default Helpers</h2>
<p>	Some helpers are available by default in every template, without the need for declaration.
<p> The following helpers are always loaded by the framework:
<ul>
	<li><b>Core</b>: Required for helper inclusion</li>
	<li><b>Tag</b>: Basic html tag helper</li>
	<li><b>Url</b>: Links and URL management helpers</li>
	<li><b>Asset</b>: Helpers populating the HTML <head> section</li>
</ul>

<p> Standard helpers are also configurable in the <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?> file. Helpers declared in
    the <b>standard_helpers</b> setting will become available in every template:
<?php pre_start('info') ?>

  // Always load the Form and Partial helpers 
  'standard_helpers' => array
  (
    'Form',
    'Partial'
  ),

<?php pre_end() ?>


<h2>Examples</h2>
<p> Use a specific helper group in this template:
<?php pre_start() ?>
  &lt;?php use_helper(<em>'Form'</em>) ?>
  ...
  &lt;?php echo <em>form_input</em>('firstname', '', array('style' => 'width:200px')) ?>
<?php pre_end() ?>

<h2>Use Helper Outside of Template</h2>
<p> If you need to use a helper outside of a template, you can use:
<?php pre_start() ?>
  // load the Text helper	
  coreToolkit::loadHelpers('Text') 
<?php pre_end() ?>
