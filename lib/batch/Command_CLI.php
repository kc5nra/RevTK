<?php
/**
 * A base class for creating command line tools, with a consistent interface.
 * 
 * Common options:
 *   --v         Verbose mode
 *   --help      Show help
 * 
 * Basic methods:
 *   getOption($name[, $default])     Return option, or default value, or throws an error
 *   throwError($message[, args])     Writes messages to STDERR and quits
 *   verbose($message[, args])        Sprintf style message display only in verbose mode (--v)
 *
 * Useful methods:
 *   getRelativePathFrom($path, $base)
 * 
 * TODO
 *   --app <app>       Sets application (changes the settings file, defaults to "revtk")
 *   --env <env>       Sets environment (changes the environment, defaults to "dev")
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

  const
    /**
     * Used with rtrim() or ltrim() to clean the ends of path names.
     */
    SLASHES_WHITESPACE  = " \t\n\r\\/";
  
  /**
   * Description of the command line tool, including list of flags.
   *
   * Below is an example, you should override it in the extending class.
   *
   * This array of data will be formatted in a consistent way for all command
   * line tools. The verbose flag (--v) will always be added at the end of the list
   * of flags.
   *
   */
  protected
    $cmdHelp = array(
      'short_desc' => 'Example file to create command line tools.',
      'usage_fmt'  => '[--v] --foo <file> [--bar]',
      'options'    => array(
        'foo <file>'     => "Description of the option flag. Descriptions spanning multiple\nlines can use the newline character.",
        'bar'            => 'Enable bar mode (defaults to "baz").'
      )
    );
  
  /**
   * Remember to call parent::init() first when you override this!
   *
   */
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
    global $argv;
    
    define('COL_ALIGN', 24);
    define('OPT_PREFIX', '  --');

    if (!isset($this->cmdHelp)) {
      $this->throwError('Help text must be declared in extending class, see '.__CLASS__.' documentation.');
    }

    $help = $this->cmdHelp;
    
    // short description
    echo $help['short_desc']."\n\n";

    // sample command line
    echo 'php ' . $argv[0] . ' ' . $help['usage_fmt']."\n\n";

    // print out list of flags
    foreach ($help['options'] as $optFlag => $optDesc)
    {
      $align  = max(COL_ALIGN - strlen(OPT_PREFIX) - strlen($optFlag), 1);

      // align newlines to the description column
      $optDesc = preg_replace('/\n/', "\n".str_repeat(' ', COL_ALIGN), $optDesc);

      echo OPT_PREFIX.$optFlag.str_repeat(' ', $align).$optDesc."\n";
    }

    // add the verbose flag to the list
    echo "\n".OPT_PREFIX.'v'.str_repeat(' ', COL_ALIGN - strlen(OPT_PREFIX) - 1).'Verbose mode (off by default)'."\n";
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

  /**
   * Prints out sprintf style error message to STDERR and exits.
   * 
   * @param string $message
   * @param mixed  $arguments   Variable number of sprintf style arguments
   *
   * @return void
   */
  protected function throwError()
  {
    $args = func_get_args();
    $message = call_user_func_array('sprintf', $args) . "\n";
    fwrite(STDERR, 'Error: ' . $message);
    exit(-1);
  }
  
  /**
   * If verbose flag is set, prints a sprintf style message, otherwise do nothing.
   *
   * @param string $message
   * @param mixed  $arguments   Variable number of sprintf style arguments
   * 
   * @return void
   */
  protected function verbose()
  {
    $args = func_get_args();
    if ($this->isVerbose) {
      $message = call_user_func_array('sprintf', $args) . "\n";
      fwrite(STDERR, $message);
    }
  }

  /**
   * Return a relative path from an absolute path, given the base path.
   * 
   * @param  string $path  Fully qualified source path, can include filename
   * @param  string $base  Fully qualified base path (no filename)
   *
   * @return string        Relative path, without leading separator
   */
  protected function getRelativePathFrom($path, $base)
  {
    $pos = strpos($path, $base);
    if ($pos === false || $pos !== 0) {

      $this->throwError('getRelativePathFrom() path (%s) does not start with base (%s)', $path, $base);
    }
    
    $relPath = substr($path, strlen($base));
    
    return ltrim($relPath, self::SLASHES_WHITESPACE);
  }
}

