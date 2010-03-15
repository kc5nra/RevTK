<?php
/**
 * This helper loads other helpers.
 * 
 * @package    Core
 * @subpackage helper
 * @author     Fabrice Denis
 */

/**
 * Load a custom helper file for use in the current template.
 * 
 * The file should be in the /lib/helper/ directory and the filename is <helpername>Helper.php
 * 
 * @param string  One or multiple helpers to load.
 * @return 
 */
function use_helper()
{
  coreToolkit::loadHelpers(func_get_args());
}
