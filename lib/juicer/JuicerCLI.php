<?php
/**
 * Command line interface to the Juicer tool.
 * 
 * JuicerCLI provides a wrapper for the Juicer tool, that allows to create the output files from
 * the command line. The output files should then be minified with a separate program such as
 * YUI Compressor, or Google Closure compiler. 
 * 
 * Command line options:
 *   See showHelp() method or use --help.
 * 
 * Public methods:
 *   getOption(name, default)    Allows Juicer instance to read command line flags
 * 
 * Config file example (juicer.config.php):
 * 
 *   return array(
 *     // %YUI% will point to the source YUI Library folder
 *     'YUI' => 'C:\Dev\Frameworks\yui\build',
 *     // assets copied from the YUI library will go into a "yui3" folder of the document root 
 *     'MAPPINGS' => array(
 *        'C:\Dev\Frameworks\yui\build' => 'yui3'
 *     )
 *   );
 * 
 * Example (js):
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config config/juicer.config.php --infile web/js/demo.juicy.js
 * Example (css):
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config config/juicer.config.php --infile web/css/demo.juicy.css
 *  
 * @author   Fabrice Denis
 * @date     20 Nov 2009
 * @license  Please view the LICENSE file that was distributed with this source code.
 */

define('CORE_ROOT_DIR', realpath(dirname(__FILE__).'/../../'));
require_once(CORE_ROOT_DIR.'/lib/args/SimpleArgs.php');
require_once(CORE_ROOT_DIR.'/lib/juicer/Juicer.php');

class Juicer_CLI
{
  private 
    $isVerbose = false,
    $args      = null;
  
  public function init()
  {
    $this->args = new SimpleArgs();

    if ($this->args->flag('help')) {
      $this->showHelp();
      exit();
    }

    $this->isVerbose = $this->args->flag('v') !== false;
    $this->verbose("Verbose: ON");
    
    // check parameters
    $webRoot = $this->args->flag('webroot');
    $infile = $this->args->flag('infile'); 
    if (!is_string($webRoot) || !is_string($infile)) {
      $this->throwError("Missing argument(s)");
    }

    if (!file_exists($infile)) {
      $this->throwError("File not found: %s", $infile);
    }
    
    // set webroot qualified path
    $webRoot = realpath($webRoot);
    
    // constants file
    $constants = array();
    $configFile = $this->args->flag('config');
    if (!file_exists($configFile)) {
      $this->throwError("File not found: %s", $configFile);
    }
    else {
      $constants = require($configFile);
      $this->verbose('Constants: %s', implode(', ', array_keys($constants)));
    }

    $options = array(
      'VERBOSE'    => $this->isVerbose,
      'STRIP'      => $this->args->flag('strip'),
      'WEB_PATH'   => $webRoot,
      'WEB_EXCL'   => '*.psd,*.txt,*.bak,*.css,*.js',
      'CLI'        => $this   // set if using Juicer from the command line
    );
    
    // auto output file naming
    if (false !== strstr($infile, Juicer::FILE_PATTERN_JUICY)) {
      $outfile = str_replace(Juicer::FILE_PATTERN_JUICY, Juicer::FILE_PATTERN_JUICED, $infile);
    }
    else {
      $this->throwError('The default output file naming requires "*.juicy.*" pattern.');
    }
  
    $juicer = new Juicer($options, $constants);
    
    // start profiling script speed
    $juicer->profileStart();

    try {
      $contents = $juicer->juice($infile);
    }
    catch (Exception $e) {
      $this->throwError('EXCEPTION: ' . $e->getMessage());
    }

    // end profiling script speed
    $this->verbose('Execution time: %s seconds.', $juicer->profileEnd());

    $this->verbose('Output file: "%s".', $outfile);
    
    if (file_put_contents($outfile, $contents)===false)
    {
      $this->throwError("Error writing to outfile %s", $outfile);
    }
    
    $this->verbose('Success!');
  }

  private function showHelp()
  {
    echo <<<EOL
Preprocesses and concatenates javascript and stylesheets.

php JuicerCLI.php --webroot <path> --config <file> --infile <file> [--strip <method>] [--v] [--list]

  --config <file>     Configuration file in php as an array of key => values
  --webroot <path>    Path to the document root of the web server (can be realative)
  --infile <file>     A javascript or stylesheet file to parse
  
  --strip <method>    Strip all calls to method from output file (eg. "console.log")
  
  --v                 Verbose mode (off by default)
  --list              List all assets used in stylesheets and the remapped web folder 

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
  public function getOption($name, $default = null)
  {
    $value = $this->args->flag($name);
    if ($value === false && $default === null) {
      $this->throwError(' Option "%s" required but not set.', $name);
    }
    return $value !== false ? $value : $default;
  }

  private function throwError()
  {
    $args = func_get_args();
    $message = call_user_func_array('sprintf', $args) . "\n";
    fwrite(STDERR, 'Error: ' . $message);
    exit(-1);
  }
  
  private function verbose()
  {
    $args = func_get_args();
    if ($this->isVerbose) {
      $message = call_user_func_array('sprintf', $args) . "\n";
      fwrite(STDERR, $message);
    }
  }
} 

$jshell = new Juicer_CLI();
$jshell->init();
