<?php
/**
 * Helpers for including headers, metas, javascripts etc.
 * 
 * Most of these helpers are used to output settings from coreWebResponse.
 * The coreWebResponse can be set in code or configured via the view config files.
 * 
 * @package    Core
 * @subpackage helper
 * @author     Fabrice Denis
 * @copyright  Based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

/**
 * Returns a <script> include tag per source given as argument.
 *
 * <b>Examples:</b>
 * <code>
 *  echo javascript_include_tag('xmlhr');
 *    => <script language="JavaScript" type="text/javascript" src="/js/xmlhr.js"></script>
 *  echo javascript_include_tag('common.javascript', '/elsewhere/cools');
 *    => <script language="JavaScript" type="text/javascript" src="/js/common.javascript"></script>
 *       <script language="JavaScript" type="text/javascript" src="/elsewhere/cools.js"></script>
 * </code>
 *
 * @param  string asset names
 * @return  string XHTML compliant <script> tag(s)
 * @see    javascript_path 
 */
function javascript_include_tag()
{
  $sources = func_get_args();
  $sourceOptions = (func_num_args() > 1 && is_array($sources[func_num_args() - 1])) ? array_pop($sources) : array();

  $html = '';
  foreach ($sources as $source)
  {
    $options = array_merge(array('type' => 'text/javascript', 'src' => $source), $sourceOptions);
    $html .= '  '.content_tag('script', '', $options)."\n";
  }

  return $html;  
}

/**
 * Returns a css <link> tag per source given as argument,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Options:</b>
 * - rel - defaults to 'stylesheet'
 * - type - defaults to 'text/css'
 * - media - defaults to 'screen'
 *
 * <b>Examples:</b>
 * <code>
 *  echo stylesheet_tag('style');
 *    => <link href="/stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('style', array('media' => 'all'));
 *    => <link href="/stylesheets/style.css" media="all" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('style', array('raw_name' => true));
 *    => <link href="style" media="all" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('random.styles', '/css/stylish');
 *    => <link href="/stylesheets/random.styles" media="screen" rel="stylesheet" type="text/css" />
 *       <link href="/css/stylish.css" media="screen" rel="stylesheet" type="text/css" />
 * </code>
 *
 * @param  string asset names
 * @param  array additional HTML compliant <link> tag parameters
 * @return string XHTML compliant <link> tag(s)
 * @see    stylesheet_path 
 */
function stylesheet_tag()
{
  $sources = func_get_args();
  $sourceOptions = (func_num_args() > 1 && is_array($sources[func_num_args() - 1])) ? array_pop($sources) : array();

  $html = '';
  foreach ($sources as $source)
  {
    //$source = stylesheet_path($source, $absolute);
    $options = array_merge(array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen', 'href' => $source), $sourceOptions);

    $html .= '  '.tag('link', $options)."\n";
  }

  return $html;
}

/**
 * Prints a set of <meta> tags according to the response attributes,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Examples:</b>
 * <code>
 *  include_metas();
 *    => <meta name="title" content="symfony - open-source PHP5 web framework" />
 *       <meta name="robots" content="index, follow" />
 *       <meta name="description" content="symfony - open-source PHP5 web framework" />
 *       <meta name="keywords" content="symfony, project, framework, php, php5, open-source, mit, symphony" />
 *       <meta name="language" content="en" /><link href="/stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
 * </code>
 *
 * <b>Note:</b> Modify the view.yml or use sfWebResponse::addMeta() to change, add or remove metas.
 *
 * @return string XHTML compliant <meta> tag(s)
 * @see    include_http_metas 
 * @see    sfWebResponse::addMeta()
 */
function include_metas()
{
  $context = coreContext::getInstance();
  
  foreach ($context->getResponse()->getMetas() as $name => $content)
  {
    echo '  <meta name="'.$name.'" content="'.$content.'" />'."\n";
  }
}

/**
 * Returns a set of <meta http-equiv> tags according to the response attributes,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Examples:</b>
 * <code>
 *  include_http_metas();
 *    => <meta http-equiv="content-type" content="text/html; charset=utf-8" />
 * </code>
 *
 * <b>Note:</b> Modify the view.yml or use sfWebResponse::addMeta() to change, add or remove HTTP metas.
 *
 * @return string XHTML compliant <meta> tag(s)
 * @see    include_metas
 * @see    sfWebResponse::addHttpMeta()
 */
function include_http_metas()
{
  foreach (coreContext::getInstance()->getResponse()->getHttpMetas() as $httpequiv => $value)
  {
    echo '  <meta http-equiv="'.$httpequiv.'" content="'.$value.'" />'."\n";
  }
}

/**
 * Returns the title of the current page according to the response attributes,
 * to be included in the <title> section of a HTML document.
 *
 * <b>Note:</b> Modify the coreWebResponse object or the view config file to modify the title of a page.
 *
 * @return string page title
 */
function include_title()
{
  $title = coreContext::getInstance()->getResponse()->getTitle();
  
  echo '  <title>'.$title.'</title>'."\n";
}

/**
 * Returns <script> tags for all javascripts configured in view config or added to the response object.
 *
 * @return string <script> tags
 */
function include_javascripts()
{
  $response = coreContext::getInstance()->getResponse();

  $already_seen = array();
  $html = '';

  foreach ($response->getPositions() as $position)
  {
    foreach ($response->getJavascripts($position) as $files => $options)
    {
      if (!is_array($files))
      {
        $files = array($files);
      }

      foreach ($files as $file)
      {
        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;
        $html .= javascript_include_tag($file, $options);
      }
    }
  }

  echo $html;
}

/**
 * Adds a stylesheet to the response object.
 *
 * @see coreWebResponse->addStylesheet()
 */
function use_stylesheet($css, $position = '', $options = array())
{
  coreContext::getInstance()->getResponse()->addStylesheet($css, $position, $options);
}

/**
 * Adds a javascript to the response object.
 *
 * @see coreWebResponse->addJavascript()
 */
function use_javascript($js, $position = '', $options = array())
{
  coreContext::getInstance()->getResponse()->addJavascript($js, $position, $options);
}

/**
 * Prints <link> tags for all stylesheets configured in view.yml or added to the response object.
 *
 * @see get_stylesheets()
 */
function include_stylesheets()
{
  $response = coreContext::getInstance()->getResponse();

  $already_seen = array();
  $html = '';

  foreach ($response->getPositions() as $position)
  {
    foreach ($response->getStylesheets($position) as $files => $options)
    {
      //DBG::out('POSITION '.$position.' FILES '.$files.' OPTIONS '.print_r($options,true));

      if (!is_array($files))
      {
        $files = array($files);
      }

      foreach ($files as $file)
      {
        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;
        $html .= stylesheet_tag($file, $options);
      }
    }
  }
  
  echo $html;
}

/**
 * Returns an <img> image tag for the asset given as argument.
 *
 * <b>Options:</b>
 * - 'alt'  - defaults to the file name part of the asset (capitalized and without the extension)
 * - 'size' - Supplied as "XxY", so "30x45" becomes width="30" and height="45"
 *
 * <b>Examples:</b>
 * <code>
 *  echo image_tag('foobar.gif');
 *    => <img src="foobar.gif" alt="Foobar" />
 *  echo image_tag('/my_images/image.gif', array('alt' => 'Alternative text', 'size' => '100x200'));
 *    => <img src="/my_images/image.gif" alt="Alternative text" width="100" height="200" />
 * </code>
 *
 * @param  string image asset name
 * @param  array additional HTML compliant <img> tag parameters
 * @return  string XHTML compliant <img> tag
 */
function image_tag($source, $options = array())
{
  if (!$source)
  {
    return '';
  }

  $options = _parse_attributes($options);

  // removed Symfony's 'absolute' option

  // removed Symfony's compute path
  $options['src'] = $source;

  if (!isset($options['alt']))
  {
    $path_pos = strrpos($source, '/');
    $dot_pos = strrpos($source, '.');
    $begin = $path_pos ? $path_pos + 1 : 0;
    $nb_str = ($dot_pos ? $dot_pos : strlen($source)) - $begin;
    $options['alt'] = ucfirst(substr($source, $begin, $nb_str));
  }

  if (isset($options['size']))
  {
    list($options['width'], $options['height']) = explode('x', $options['size'], 2);
    unset($options['size']);
  }

  return tag('img', $options);
}
