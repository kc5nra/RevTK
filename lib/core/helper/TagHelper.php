<?php
/**
 * Some base helpers to construct html tags.
 * 
 * @author     Fabrice Denis
 * @package    Core
 * @subpackage Helper
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

/**
 * Escapes an HTML string.
 * Note: removed fix_double_escape() call, why does Symfony use this?
 * 
 * @param  string HTML string to escape
 * @return string escaped string
 */
function escape_once($html)
{
	return htmlspecialchars($html, ENT_QUOTES, coreConfig::get('sf_charset'));
}

/**
 * Fixes double escaped strings.
 * 
 * @param  string HTML string to fix
 * @return string escaped string
 */
function fix_double_escape($escaped)
{
  return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
}

/**
 * Constructs an html tag.
 *
 * @param  $name    string  tag name
 * @param  $options array   tag options
 * @param  $open    boolean true to leave tag open
 * @return string
 */
function tag($name, $options = array(), $open = false)
{
  if (!$name)
  {
    return '';
  }

  return '<'.$name._tag_options($options).(($open) ? '>' : ' />');
}

function content_tag($name, $content = '', $options = array())
{
  if (!$name)
  {
    return '';
  }

  return '<'.$name._tag_options($options).'>'.$content.'</'.$name.'>';
}


/**
 * Converts specific <i>$options</i> to their correct HTML format
 * 
 * Adapted from Symfony Copyright Fabien Potencier (www.symfony-project.org)
 *
 * @param  array options
 * @return array returns properly formatted options 
 */
function _convert_options($options)
{
  $options = _parse_attributes($options);

  foreach (array('disabled', 'readonly', 'multiple') as $attribute)
  {
    if (array_key_exists($attribute, $options))
    {
      if ($options[$attribute])
      {
        $options[$attribute] = $attribute;
      }
      else
      {
        unset($options[$attribute]);
      }
    }
  }

  return $options;
}

/**
 * Turns associative array of options into a string of html tag attributes.
 * 
 * @param  array  Associative array of tag options
 * @return tring  String in the form xxx="yyy", with attributes escaped for html
 */
function _tag_options($options = array())
{
	$options = _parse_attributes($options);
	
	$html = '';
	foreach ($options as $key => $value)
	{
		$html .= ' '.$key.'="'.escape_once($value).'"';
	}
	
	return $html;
}

/**
 * Converts options in query string format, into an associative array.
 * This allows many helpers to accept options in the html attribute format: xxx="yyy" aaa="bbb" ...
 * 
 */
function _parse_attributes($string)
{
    return is_array($string) ? $string : coreToolkit::stringToArray($string);
}

function _get_option(&$options, $name, $default = null)
{
  if (array_key_exists($name, $options))
  {
    $value = $options[$name];
    unset($options[$name]);
  }
  else
  {
    $value = $default;
  }

  return $value;
}
