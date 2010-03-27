<h2>UrlHelper</h2>

<p>Functions available:
<?php pre_start() ?>
// Returns a routed URL based on the module/action passed as argument
<em>string</em> function url_for($internal_uri, $absolute = false)

// Creates a &lt;a&gt; link tag of the given name using a routed URL
// based on the module/action passed as argument and the routing configuration.
// 
// If null is passed as a name, the link itself will become the name.
// Options:
//  'absolute'     - If set to true, the helper outputs an absolute URL
//  'query_string' - To append a query string to the routed url
//                   The query string should be encoded (eg. http_build_query())
<em>string</em> function link_to($name = '', $internal_uri = '', $options = array())
<?php pre_end() ?>

<h2>Examples</h2>

<p>
  Go to <a href="<?php echo url_for('default/index') ?>">homepage</a> using "module/action".<br />
  Go to <?php echo link_to('homepage', '@homepage') ?> using named route "@homepage".<br />
  Go to <a href="<?php echo url_for('my_module/index') ?>">my_module</a> using default index action.<br />
  Go to <a href="<?php echo url_for('news/index?year=2007&month=5&day=2') ?>">news/2007/5/2</a>.<br />
  Go to <a href="<?php echo url_for('utf8/index?japanese=記事') ?>">url with kanji</a>.<br />
  <br />
  Link to <?php echo link_to('News 2008/11 [absolute url]', 'news/index?year=2008&month=11', array('absolute'=>true)) ?><br />
  
  Link to <?php echo link_to('Named route', '@news_archive_y_m') ?> (uses default values for url parameters)<br />
  Link to <?php echo link_to('Absolute url', 'http://www.google.com') ?><br />
  Link to <?php echo link_to('using attributes in link tag', 'default/index', array('title'=>'Hi there', 'class'=>'green-arrow')) ?><br />
  Wrap image with link: <?php echo link_to('<img src="x.gif" width="10" height="10" alt="[link with image]" border="1" />', 'default/index' ) ?><br />
</p>

<h2>Using sfRouting to parse a Url</h2>

<p> Parsing the relative part of the external uri, returns an array of parameters:
<?php 
  $routing = coreContext::getInstance()->getRouting();
  pre_start('printr'); print_r($routing->parse('my_module/my_action')); pre_end();
?>
