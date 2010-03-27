<h2>TagHelper</h2>

<p> Basic tag helper, used by other helpers.

<p> All values are escaped by default, except <var>$content</var> in content_tag.

<p>Functions available:
<?php pre_start() ?>
// Returns escaped html string using htmlspecialchars() & ENT_QUOTES
function escape_once($html)

// Create a tag, can be left open, options are escaped
function tag($name, $options = array(), $open = false)

// Create a content tag (opening and closing tag), options are escaped
//  note that <b>$content is not escaped</b>
function content_tag($name, $content = '', $options = array())

// These functions can be used when building other helpers:

// Converts options in query string format to an associative array
// 'xxx="yyy" aaa="bbb"'  =>  array('xxx' => 'yyy', 'aaa' => 'bbb')
_parse_attributes($string)
<?php pre_end() ?>

<h2>Examples</h2>

<p> Note: use FormHelper where possible, which handles escaping for you:
<?php pre_start() ?>
&lt;?php echo tag('input', array('name' => 'foo', 'type' => 'text')) ?>
&lt;?php echo content_tag('textarea', escape_once('dummy content'), 'name=foo') ?>
<?php pre_end() ?>

<p> For attributes <b>disabled</b>, <b>readonly</b> and <b>multiple</b>, specify 'true':
<?php pre_start() ?>
&lt;?php echo form_input('foo', '', array('readonly' => true)) ?>
<?php pre_end() ?>
