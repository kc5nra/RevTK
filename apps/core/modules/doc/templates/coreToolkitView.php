<h2>coreToolkit</h2>

<p> coreToolkit contains utility functions, as static helpers.

<?php pre_start() ?>
// Load one or more helpers directly, use in action/component code, before the
// use_helper() helper is available in the view template.
static function loadHelpers($helpers)

// Determine if a filesystem path is absolute.
static function isPathAbsolute($path)

// Strip slashes recursively from array
static function stripslashesDeep($value)

static function arrayDeepMerge()

// Converts a string of attribute="value" pairs into an associative array
static public function stringToArray($string)
<?php pre_end() ?>

<h2>Using helpers outside a template</h2>

<p> If you ever need to use a helper outside a template, you can still load 
    a helper group from anywhere by calling <em>coreToolkit::loadHelpers($helpers)</em>
    where $helpers is a helper group name or an array of helper group names.

<p> For instance, if you want to use truncate_text() in an action, you need to call:
<?php pre_start() ?>
coreToolkit::loadHelpers('Text');
<?php pre_end() ?>
