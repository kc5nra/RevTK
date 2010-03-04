<?php
/**
 * Helpers to output dates in templates.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 * @copyright  Code based on Symfony php framework, by Fabien Potencier (www.symfony-project.org)
 */

/**
 * Placeholder to use for Symfony's corresponding helper. We ignore the 
 * culture and charset arguments. We also use php date() format directly,
 * but this should be easier to upgrade if we move on to the international
 * date support of Symfony.
 * 
 * The default date format is in the form '23/01/2008'.
 * 
 * @return string  Formatted date time, or empty string if conversion error.
 */
function format_date($date, $format = 'd-m-Y', $culture = null, $charset = null)
{
	if (is_null($date))
	{
		return '';
	}
	
	if (is_string($date))
	{
		$date = strtotime($date);
		if ($date===false || $date===-1)
		{
			return 'x';
		}
	}

	$s_date = date($format, $date);
	
	return is_string($s_date) ? $s_date : '';
}
 

function distance_of_time_in_words($from_time, $to_time = null, $include_seconds = false)
{
  $to_time = $to_time ? $to_time : time();

  $distance_in_minutes = floor(abs($to_time - $from_time) / 60);
  $distance_in_seconds = floor(abs($to_time - $from_time));

  $string = '';
  $parameters = array();

  if ($distance_in_minutes <= 1)
  {
    if (!$include_seconds)
    {
      $string = $distance_in_minutes == 0 ? 'less than a minute' : '1 minute';
    }
    else
    {
      if ($distance_in_seconds <= 5)
      {
        $string = 'less than 5 seconds';
      }
      else if ($distance_in_seconds >= 6 && $distance_in_seconds <= 10)
      {
        $string = 'less than 10 seconds';
      }
      else if ($distance_in_seconds >= 11 && $distance_in_seconds <= 20)
      {
        $string = 'less than 20 seconds';
      }
      else if ($distance_in_seconds >= 21 && $distance_in_seconds <= 40)
      {
        $string = 'half a minute';
      }
      else if ($distance_in_seconds >= 41 && $distance_in_seconds <= 59)
      {
        $string = 'less than a minute';
      }
      else
      {
        $string = '1 minute';
      }
    }
  }
  else if ($distance_in_minutes >= 2 && $distance_in_minutes <= 44)
  {
    $string = '%minutes% minutes';
    $parameters['%minutes%'] = $distance_in_minutes;
  }
  else if ($distance_in_minutes >= 45 && $distance_in_minutes <= 89)
  {
    $string = 'about 1 hour';
  }
  else if ($distance_in_minutes >= 90 && $distance_in_minutes <= 1439)
  {
    $string = 'about %hours% hours';
    $parameters['%hours%'] = round($distance_in_minutes / 60);
  }
  else if ($distance_in_minutes >= 1440 && $distance_in_minutes <= 2879)
  {
    $string = '1 day';
  }
  else if ($distance_in_minutes >= 2880 && $distance_in_minutes <= 43199)
  {
    $string = '%days% days';
    $parameters['%days%'] = round($distance_in_minutes / 1440);
  }
  else if ($distance_in_minutes >= 43200 && $distance_in_minutes <= 86399)
  {
    $string = 'about 1 month';
  }
  else if ($distance_in_minutes >= 86400 && $distance_in_minutes <= 525959)
  {
    $string = '%months% months';
    $parameters['%months%'] = round($distance_in_minutes / 43200);
  }
  else if ($distance_in_minutes >= 525960 && $distance_in_minutes <= 1051919)
  {
    $string = 'about 1 year';
  }
  else
  {
    $string = 'over %years% years';
    $parameters['%years%'] = round($distance_in_minutes / 525960);
  }

  return strtr($string, $parameters);
}

// Like distance_of_time_in_words, but where to_time is fixed to time()
function time_ago_in_words($from_time, $include_seconds = false)
{
  return distance_of_time_in_words($from_time, time(), $include_seconds);
}
