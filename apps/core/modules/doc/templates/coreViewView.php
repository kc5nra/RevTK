<h2>coreView</h2>

<p> A view that uses PHP as the templating engine.

<?php pre_start() ?>
// Return values for actions
const NONE = 'None';            // Skip the view template
const ERROR = 'Error';          // Show an error view
const SUCCESS = 'Success';      // Show a success view

// If using setTemplate() afterwards, the last three arguments can be omitted
function __construct($context, $moduleName = '', $actionName = '', $viewName = '')
// Get the parameter holder of this view
function getParameterHolder()
// Set a template file explicitly (relative or absolute path)
function setTemplate($template)
// Activate a decorator template file for this view.
function setDecoratorTemplate($templateName)
// Check whether this teplate has a decorator set
// @return boolean True if this view has a decorator 
function hasDecorator()
// Render the presentation
// @return string
function render()
<?php pre_end() ?>

<h2>Template Variables</h2>

<p> All variables set through <em>$this</em> in <b>actions</b> and <b>components</b> become variables of the template.

<p> There are also standard variables available in all coreView templates:

<pre class="info">
<b>$_context</b>  : <?php echo link_to('coreContext','doc/core?include_name=context') ?> object
<b>$_request</b>  : <?php echo link_to('coreRequest','doc/core?include_name=request') ?> object
<b>$_params</b>   : <?php echo link_to('sfParameterHolder', 'doc/lib?page_id=sfParameterHolder') ?>, shortcut to $_request->getParameterHolder()
<b>$_user</b>     : <?php echo link_to('coreUser','doc/core?include_name=user') ?> object
<b>$_response</b> : <?php echo link_to('coreWebResponse','doc/core?include_name=webresponse') ?> object
</pre>

<h2>Decorator</h2>

<p> A view can be decorated with another template file.<br/>
    The decorator template uses the variable <b>$core_content</b> to include the content:
<?php pre_start() ?>
...
&lt;?php echo <em>$core_content</em> ?&gt;
...
<?php pre_end() ?>

<p> The site layout is a decorator template applied to the action's view.<br/>
    It can be set through <?php echo link_to('view configuration', 'doc/misc?page_id=viewconfig') ?>, or in the action:
<?php pre_start() ?>
// Set what layout template to use at run time
$this->setLayout('layout');
<?php pre_end() ?>

<p> Adding a decorator to a view:
<?php pre_start() ?>
$viewInstance = new coreView(coreContext::getInstance(), 'module', 'action', 'viewname');
$viewInstance->setDecoratorTemplate('mydecorator');
<?php pre_end() ?>

