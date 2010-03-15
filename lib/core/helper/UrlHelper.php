<?php
/**
 * Helpers to create routed urls based on internal links (module/action).
 * 
 * @package    Core
 * @subpackage helper
 * @author     Fabrice Denis
 * @copyright  Based on Symfony php framework, (c) Fabien Potencier (www.symfony-project.org)
 */
 
/**
 * Returns a routed URL based on the module/action passed as argument
 * and the routing configuration.
 *
 * Examples:
 *   echo url_for('my_module/my_action');
 *    => /path/to/my/action
 *
 * @param  string 'module/action'
 * @param  bool   Return absolute path?
 * @return string Routed URL
 */
function url_for($internal_uri, $absolute = false)
{
  static $controller;
  
  if (!isset($controller))
  {
    $controller = coreContext::getInstance()->getController();
  }
  
  return $controller->genUrl($internal_uri, $absolute);
}

/**
 * Creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * 
 * If null is passed as a name, the link itself will become the name.
 * 
 * Examples:
 *  echo link_to('Homepage', 'default/index')
 *    => <a href="/">Homepage</a>
 *  
 *  echo link_to('News 2008/11', 'news/index?year=2008&month=11')
 *    => <a href="/news/2008/11">News 2008/11</a>
 *  
 *  echo link_to('News 2008/11 [absolute url]', 'news/index?year=2008&month=11', array('absolute'=>true))
 *    => <a href="http://myapp.example.com/news/2008/11">News 2008/11 [absolute url]</a>
 *  
 *  echo link_to('Absolute url', 'http://www.google.com')
 *    => <a href="http://www.google.com">Absolute url</a>
 *  
 *  echo link_to('Link with attributes', 'default/index', array('id'=>'my_link', 'class'=>'green-arrow'))
 *    => <a id="my_link" class="green-arrow" href="/">Link with attributes</a>
 *  
 *  echo link_to('<img src="x.gif" width="150" height="100" alt="[link with image]" />', 'default/index' )
 *    => <a href="/"><img src="x.gif" width="150" height="100" alt="[link with image]" /></a>
 *    
 * 
 * Options:
 *   'absolute'     - if set to true, the helper outputs an absolute URL
 *   'query_string' - to append a query string (starting by ?) to the routed url
 *   'anchor'       - to append an anchor (starting by #) to the routed url
 * 
 * @param  string  text appearing between the <a> tags
 * @param  string  'module/action' or '@rule' of the action, or an absolute url
 * @param  array   additional HTML compliant <a> tag parameters
 * @return string  XHTML compliant <a href> tag
 * @see url_for
 */
function link_to($name = '', $internal_uri = '', $options = array())
{
  $html_options = _parse_attributes($options);

  $absolute = false;
  if (isset($html_options['absolute']))
  {
    $absolute = (boolean) $html_options['absolute'];
    unset($html_options['absolute']);
  }

  // Fabrice: FIXME (genUrl() doesnt like '#anchor' ?) => ignore empty string
  $html_options['href'] = ($internal_uri !== '') ? url_for($internal_uri, $absolute) : '';

  // anchor
  if (isset($html_options['anchor']))
  {
    $html_options['href'] .= '#'.$html_options['anchor'];
    unset($html_options['anchor']);
  }

  if (isset($html_options['query_string']))
  {
    $html_options['href'] .= '?'.$html_options['query_string'];
    unset($html_options['query_string']);
  }

  if (is_object($name))
  {
    if (method_exists($name, '__toString'))
    {
      $name = $name->__toString();
    }
    else
    {
      DBG::error(sprintf('Object of class "%s" cannot be converted to string (Please create a __toString() method).', get_class($name)));
    }
  }

  if (!strlen($name))
  {
    $name = $html_options['href'];
  }
  
  return content_tag('a', $name, $html_options);
}
