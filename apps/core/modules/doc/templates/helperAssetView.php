<h2>AssetHelper</h2>

<p> Helpers populating the HTML &lt;head&gt; section, and providing easy links to external assets (images, JavaScript, and style sheet files).

<p>Functions available:
<?php pre_start() ?>
// Output html head tags according what was set in the response

// Output http-equiv meta tags set by addHttpMeta(), if you don't use this helper
// the values set by addHttpMeta() will still show up in the HTTP response headers.
function include_http_metas()

// Output meta tags set by addMeta()
function include_metas()

// Output title set by setTitle(), note that the title also show as a meta tag
function include_title()

// Output javascript and stylesheets tags
function include_javascripts()
function include_stylesheets()

// Add javascripts and stylesheets to the response from any template
use_stylesheet($css, $position = '', $options = array())
use_javascript($js, $position = '', $options = array())

// Returns a stylesheet &lt;link&gt; tag
function stylesheet_tag('styles.css', array('media' => 'print', ...))
function stylesheet_tag('styles1.css', 'styles2.css', ...)

// Returns a javascript &lt;script&gt; tag per source given as argument.
function javascript_include_tag('script1.js', ...)

// Returns an <img> image tag for asset given as argument
// The alt attribute defaults to the filename without path or extension
// The 'size' option can be used to specify width and height: '123x456'
function image_tag($source, $options = array())
<?php pre_end() ?>

<h2>Setting the HTML Head Tags</h2>

<p> Sample HTML document head, using the Asset helpers:

<pre>
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
&lt;html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
&lt;head>
<em>&lt;?php include_http_metas() ?></em>
<em>&lt;?php include_metas() ?></em>
<em>&lt;?php include_title() ?></em>
<em>&lt;?php include_stylesheets() ?></em>
<em>&lt;?php include_javascripts() ?></em>
  &lt;link rel="shortcut icon" href="favicon.ico" />
&lt;/head>
</pre>
