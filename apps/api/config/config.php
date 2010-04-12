<?php

/**
 * API Configuration.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 27 March, 2010
 * @package api
 **/

require_once(CORE_ROOT_DIR.'/lib/core/core.php');

class apiConfiguration extends coreApplicationConfiguration
{
  private
    $profile_time;

  /**
   * Configures the applicationconfiguration.
   *
   * Override this method if you want to customize your application configuration.
   */
  public function configure()
  {

    // profile php execution time
    $this->profileStart();

    // Application configuration settings
    coreConfig::set('app_zend_lib_dir', coreConfig::get('lib_dir'));

    // Integrate Zend from project level directory /lib/Zend/
    if ($sf_zend_lib_dir = coreConfig::get('app_zend_lib_dir'))
    {
      set_include_path($sf_zend_lib_dir.PATH_SEPARATOR.get_include_path());
      require_once($sf_zend_lib_dir.'/Zend/Loader.php');
      spl_autoload_register(array('Zend_Loader', 'autoload'));
    }

    // set default timezone setting, fixes php error 'date(): It is not safe to rely on the system's timezone settings'
    date_default_timezone_set('UTC');
  }

  /**
   * Record the start time (will be used to calculate the generation time for the page)
   *
   */
  public function profileStart()
  {
    list($usec, $sec) = explode(' ', microtime());
    $this->profile_time = ((float)$usec + (float)$sec);
  }

  /**
   * Return elapsed time since execution of core.php
   *
   */
  public function profileEnd()
  {
    list($usec, $sec) = explode(' ', microtime());
    $time_diff = ((float)$usec + (float)$sec) - $this->profile_time;
    return sprintf('%.3f', $time_diff);
  }

}
