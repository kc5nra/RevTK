<?php
/**
 * TextHelper.
 * 
 * Implements some helpers from Symfony TextHelper.
 * 
 * @see        http://www.symfony-project.org/api/1_1/TextHelper
 * 
 * @package    Core
 * @subpackage helper
 * @author     Fabrice Denis
 * @copyright  Based on Symfony php framework, (c) Fabien Potencier (www.symfony-project.org)
 */


/**
 * Truncates +text+ to the length of +length+ and replaces the last three characters with the +truncate_string+
 * if the +text+ is longer than +length+.
 * 
 * Note: This is the unmodified Symfony helper.
 */
function truncate_text($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
{
  if ($text == '')
  {
    return '';
  }

  $mbstring = extension_loaded('mbstring');
  if($mbstring)
  {
   @mb_internal_encoding(mb_detect_encoding($text));
  }
  $strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
  $substr = ($mbstring) ? 'mb_substr' : 'substr';

  if ($strlen($text) > $length)
  {
    $truncate_text = $substr($text, 0, $length - $strlen($truncate_string));
    if ($truncate_lastspace)
    {
      $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
    }

    return $truncate_text.$truncate_string;
  }
  else
  {
    return $text;
  }
}
