<h2>PartialHelper</h2>

<p> If the logic is lightweight, you will just want to include a template file having access to some data you pass to it.
    For that, you will use a <b>partial</b>. If the logic is heavier, you may want to separate the presentation from the logic with a <?php echo link_to('component', 'doc/core?include_name=component') ?>.

<p> A partial file name always starts with an underscore (_), and that helps to distinguish partials from templates, since they are located in the same templates/ folders.

<p> Because partials and components can be accessed from anywhere in the application, <b>variables</b> must be passed explicitly.

<p>Functions available:

<?php pre_start() ?>
// Evaluates and echoes a partial.
// The partial name is composed as follows: <var>'mymodule/mypartial'</var>.
// If the partial name doesn't include a module name,
//  then the partial file is searched for in the caller's templates/ directory.
// Use module name <var>'global'</var> to look for partial file in [myapp]/templates/.
// For a variable to be accessible to the partial, it has to be passed in the second argument.
function include_partial($templateName, $vars = array())

// Evaluates and echoes a component.
// For a variable to be accessible to the component and its partial, 
//  it has to be passed in the third argument.
function include_component($moduleName, $componentName, $vars = array())

// Begins the capturing of the slot, or set short content with $value
function slot($name, $value = null)
// Stops the content capture and save the content in the slot.
function end_slot()
// Returns true if the slot exists.
function has_slot($name)
// Evaluates and echoes a slot.
// @return True if slot exists and was included
function include_slot($name)

// Returns content of a	slot, or an empty string.
function get_slot($name)
// Evaluates partial and returns result as a string
function get_partial($templateName, $vars = array())
// Evaluates component and returns result as a string
function get_component($moduleName, $componentName, $vars = array())
<?php pre_end() ?>

<h2>Including a Partial</h2>

<p> If the template and the partial are in the same module, you can omit the module name:
<?php pre_start() ?>
&lt;?php include_partial('<b>mypartial1</b>') ?>
<?php pre_end() ?>
 
<p> Look for a template in another module, the module name is compulsory in that case:
<?php pre_start() ?>
&lt;?php include_partial('<b>foobar/</b>mypartial2') ?>
<?php pre_end() ?>
 
<p> Include a global partial template, by specifying the 'global' module:
<?php pre_start() ?>
&lt;?php include_partial('<b>global/</b>mypartial3') ?>
<?php pre_end() ?>

<h2>Including a Component</h2>

<p> See <?php echo link_to('coreComponent', 'doc/core?include_name=component') ?>.


<h2>Slots</h2>

<p> A slot is like a placeholder that you can put in any of the view elements (in the layout,
    template, or partial).</p>

<p> Slots provide a way to have default content defined in the layout, 
    which can be overridden at the template level:

<?php pre_start() ?>
// Example: include a 'sidebar' slot in the layout
&lt;div id="sidebar">
&lt;?php if (has_slot('sidebar')): ?>
    &lt;?php include_slot('sidebar') ?>
&lt;?php else: ?>
  &lt;!-- default sidebar code -->
  &lt;h1>Contextual zone&lt;/h1>
  &lt;p>This zone contains links and information
  relative to the main content of the page.&lt;/p>
&lt;?php endif; ?>
&lt;/div>
<?php pre_end() ?>

<p> Use the return value of <b>include_slot()</b> to simplify the code:</p>

<?php pre_start() ?>
&lt;div id="sidebar">
&lt;?php if (!include_slot('sidebar')): ?>
  &lt;!-- default sidebar code -->
  &lt;h1>Contextual zone&lt;/h1>
  &lt;p>This zone contains links and information
  relative to the main content of the page.&lt;/p>
&lt;?php endif; ?>
&lt;/div>
<?php pre_end() ?>

<p> Overriding the 'sidebar' slot content in a template:</p>

<?php pre_start() ?>
// ...

&lt;?php slot('sidebar') ?>
  &lt;!-- custom sidebar code for the current template-->
  &lt;h1>User details&lt;/h1>
  &lt;p>name:  &lt;?php echo $user->getName() ?>&lt;/p>
  &lt;p>email: &lt;?php echo $user->getEmail() ?>&lt;/p>
&lt;?php end_slot() ?>
<?php pre_end() ?>

<p> Slots can also be used to add some custom tags in the &lt;head&gt; section of the layout, depending
	on the content of the action (for example, a RSS feed or inline styles). 

<p>	If the content of the slot is very short, as this is the case when defining a title slot for example,
    you can simply pass the content as a second argument of the slot() method:
	</p>

<?php pre_start() ?>
&lt;?php slot('title', 'The title value') ?&gt;
<?php pre_end() ?>


<!--php
	// Partial include tests
	use_helper('Partial');

	// Include a <b>partial</b> from the same module, module name does not need to be specified:
	include_partial('partialDemo1', array('partialvar' => 'Success'));

	// Include a <b>partial</b> from another module, module must be specified:
	// The module name is compulsory in that case.
	include_partial('includes/partialDemo2', array('partialvar' => 'Success'));
 
	// Include a <b>global partial</b>, by using the "global" module name:
	include_partial('global/partialDemo3', array('partialvar' => 'Success'));

	// Component include tests

	// Include a component with one action per file (componentDemo1Component.php):
	include_component('includes', 'componentDemo1', array('include_component_var' => 'Parameter'));

	// Include components with multiple actions in one file (components.php):
	include_component('includes', 'componentDemo2', array('include_component_var' => 'Parameter'));
	include_component('includes', 'componentDemo3', array('include_component_var' => 'Parameter'));
-->
