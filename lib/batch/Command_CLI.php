<?php
/**
 * A base class for creating command line tools, with a consistent interface.
 * 
 * Common options:
 *   --v         Verbose mode
 *   --help      Show help
 * 
 * 
 * @author  Fabrice Denis
 */

// This block of code is the bootstrap for Core based apps 
define('CORE_ROOT_DIR',    realpath(dirname(__FILE__).'/../..'));
define('CORE_APP',         'revtk');
define('CORE_ENVIRONMENT', 'dev');
define('CORE_DEBUG',       true);
require_once(CORE_ROOT_DIR.'/apps/'.CORE_APP.'/config/config.php');
$configuration = new revtkConfiguration(CORE_ENVIRONMENT, CORE_DEBUG, CORE_ROOT_DIR);
coreContext::createInstance($configuration);

require_once(CORE_ROOT_DIR.'/lib/args/SimpleArgs.php');

class Command_CLI
{
  protected 
    $isVerbose = true,
    $args      = null;
  
  public function init()
  {
    $this->args = new SimpleArgs();

    if (!$this->args->getCount() || $this->args->flag('help')) {
      $this->showHelp();
      exit();
    }
    
    $this->isVerbose = $this->args->flag('v') !== false;
    $this->verbose("Verbose: ON");
  }

  /**
   * This should be overridden, included as a template.
   * 
   * @return string 
   */
  protected function showHelp()
  {
    echo <<<EOL
Command that does something and other kinds of things.

php dosomething.php [--v] --config <path> --out <file> 

  --config <file>     Lorem ipsum dolor sit amet, lorem ipsumus
  --out <file>        A javascript or ipsum dolor sit amet, lorem ipsumus
  
  --v                 Verbose mode (off by default)

EOL;
  }

  /**
   * Return command line option, or default value.
   * 
   * Throws error if option is undefined and no default value is provided. 
   * 
   * @param string $name
   * @param mixed $default
   * @return mixed 
   */
  protected function getOption($name, $default = null)
  {
    $value = $this->args->flag($name);
    if ($value === false && $default === null) {
      $this->throwError(' Option "%s" required but not set.', $name);
    }
    return $value !== false ? $value : $default;
  }

  protected function throwError()
  {
    $args = func_get_args();
    $message = call_user_func_array('sprintf', $args) . "\n";
    fwrite(STDERR, 'Error: ' . $message);
    exit(-1);
  }
  
  protected function verbose()
  {
    $args = func_get_args();
    if ($this->isVerbose) {
      $message = call_user_func_array('sprintf', $args) . "\n";
      fwrite(STDERR, $message);
    }
  }
}

