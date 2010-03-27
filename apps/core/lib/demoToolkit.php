<?php
/**
 * Miscellaneous utility functions here.
 * 
 * @package Core
 * @author  Fabrice Denis
 */

class demoToolkit
{
  static private
    $time_start;
  
  /**
   * Record the start time (will be used to calculate the generation time for the page)
   */
  public static function timeStart()
  {
    list($usec, $sec) = explode(' ', microtime());
    self::$time_start = ((float)$usec + (float)$sec);
  }

  /**
   * Return elapsed time since execution of core.php
   */
  public static function timeEnd()
  {
    list($usec, $sec) = explode(' ', microtime());
    $time_diff = ((float)$usec + (float)$sec) - self::$time_start;
    return sprintf('%.3f', $time_diff);
  }
}
