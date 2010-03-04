<?php
/**
 * Application Configuration.
 * 
 * This file bootstraps the application, which means that it does all the very basic
 * initializations to allow the application to start.
 * 
 * In this example we start the timer that shows the php execution time
 * in the page footer. The footer part of the layout template calls timeEnd()
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

require_once(CORE_ROOT_DIR.'/lib/core/core.php');

class coreConfiguration extends coreApplicationConfiguration
{
	private
		$time_start;

	/**
	 * Configures the applicationconfiguration.
	 *
	 * Override this method if you want to customize your application configuration.
	 */
	public function configure()
	{
		// start timer
		$this->timeStart();
	}
	
	/**
	 * Record the start time (will be used to calculate the generation time for the page)
	 * 
	 */
	public function timeStart()
	{
		list($usec, $sec) = explode(' ', microtime());
		$this->time_start = ((float)$usec + (float)$sec);
	}

	/**
	 * Return elapsed time since execution of core.php
	 * 
	 */
	public function timeEnd()
	{
		list($usec, $sec) = explode(' ', microtime());
		$time_diff = ((float)$usec + (float)$sec) - $this->time_start;
		return sprintf('%.3f', $time_diff);
	}
	
}
