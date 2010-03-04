<h2>FormHelper</h2>

<p>Helper functions available:

<?php pre_start() ?>
function form_tag($url_for_options = '', $options = array())

function label_for($id, $label, $options = array())

function input_tag($name, $value = null, $options = array())
function input_hidden_tag($name, $value = null, $options = array())
function input_password_tag($name, $value = null, $options = array())
function textarea_tag($name, $content = null, $options = array())
function checkbox_tag($name, $value = '1', $checked = false, $options = array())
function radiobutton_tag($name, $value = '1', $checked = false, $options = array())

// Returns a &lt;select&gt; tag, optionally comprised of &lt;option&gt; tags.
function select_tag($name, $option_tags = null, $options = array())
// Returns a formatted set of &lt;option&gt; tags based on array of value => label
function options_for_select($options = array(), $selected = '', $html_options = array())

// Returns a submit button
function submit_tag($value = 'Save changes', $options = array())

<?php pre_end() ?>

<h2>The Id Attribute</h2>

<p> With form helpers, each element in a form is given an id attribute deduced from its name attribute by default. 

<?php pre_start() ?>
// Text field (input)
&lt;?php echo input_tag('name', 'default value') ?>
// => &lt;input type="text" name="name" id="name" value="default value" />
<?php pre_end() ?>

<p> For checkboxes and radio buttons using the array notation, the id attribute is deduced from a combination of the name and value:

<?php pre_start() ?>
&lt;?php echo checkbox_tag('myCheck[]', 'cheese', true) ?>
// => &lt;input type="checkbox" name="myCheck[]" id="myCheck_cheese" value="cheese" />
<?php pre_end() ?>


	
<h2>Repopulating Form Fields</h2>

<p>The <b>input</b>, <b>checkbox</b>, <b>radio</b> and <b>textarea</b> are repopulated automatically
   through the request parameters (i.e. after form submission). The <b>$value</b> parameter is the initial value for the field.


<h2>The Form Tag</h2>

<p> The <b>form_tag()</b> Helper:

<?php pre_start() ?>
// &lt;form method="post" action="/path/to/save">
&lt;?php echo form_tag('test/save') ?>
 
// &lt;form method="get" enctype="multipart/form-data" class="simpleForm" action="/path/to/save">
&lt;?php echo form_tag('test/save', 'method=get multipart=true class=simpleForm') ?>
<?php pre_end() ?>
