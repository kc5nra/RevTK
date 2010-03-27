<?php
/**
 * Generate a data file with timestamps which can be used for revving filenames
 * of stylesheets and javascripts.
 * 
 * Locate all the CSS and JS files within the application folder tree,
 * use the last modified time to generate a version number for the resource,
 * then generate the static configuration file used by the templating
 * to insert version numbers into the urls for those CSS and JS files.
 * 
 * Example:
 * 
 *  php batch/build_app.php --webroot web --out config/versioning.inc.php
 * 
 * To do:
 * - refactor the helpers into /lib/tools/PathHelper.php
 * 
 * Someday/Maybe:
 * - If we match includeAssets to fully qualified filename, the patterns could
 *   also match directories, eg. include only files from "web/js/foo/*.js"
 * 
 * 
 * @package  Build
 * @author   Fabrice Denis
 */

// Bootstrap the Core framework in batch mode (skip the dispatch() call)
define('CORE_ROOT_DIR', realpath(dirname(__FILE__).'/..'));
define('CORE_APP',      'RevTK');
define('CORE_DEBUG',    true);

require_once(CORE_ROOT_DIR.'/lib/core/core.php');
$configuration = new coreProjectConfiguration(CORE_ROOT_DIR, CORE_APP, CORE_DEBUG);
coreContext::createInstance($configuration);

require(CORE_ROOT_DIR.'/lib/args/SimpleArgs.php');

class BuildApp
{
  protected
    $options         = array(),
    $includeAssets   = array();
  
  const
    /**
     * Files for which we save versioning information.
     * 
     */
    INCLUDE_ASSETS   = '*.css,*.js';
  
  function __construct()
  {
    $args = new SimpleArgs();
    
    if (!$args->getCount() || $args->flag('help')) {
      $this->showHelp();
      exit();
    }

    // options
    $this->options = array();
    $this->options['verbose'] = $args->flag('v') !== false;
    $this->options['list'] = $args->flag('list');

    $this->verbose("Verbose ON");

    $this->webPath = $args->flag('webroot');
    if (!$this->webPath) {
      $this->throwError('Required --webroot flag. Type --help for help.');      
    }

    // set filters
    $this->includeAssets = array();
    $filters = explode(',', self::INCLUDE_ASSETS);
    foreach ($filters as $filter)
    {
      // backslash characters in the filter that have a regexp meaning
      $filter = preg_replace('|[\[\]\$\\\\.\+\-\^]|', '\\\$0', trim($filter));
      // create a ready-to-use regexp pattern with the star meaning
      $pattern = '|^' . str_replace('*', '.+', $filter) . '$|i';
      
      $this->includeAssets[] = $pattern;
    }
    //die(implode(' - ', $this->includeAssets));

    $files = $this->crawl($this->webPath);
    if ($this->options['list']) {
      echo implode("\n", $files);
      echo sprintf("\n\n%s versioned resources.\n", count($files));
      exit();
    }

    $this->verbose("\n%s versioned resources.\n", count($files));

    $outfile = $args->flag('out');
    if (!$outfile) {
      $this->throwError('Required --out flag. Type --help for help.');
    }
    
    $contents = $this->build($files);

    if (false === file_put_contents($outfile, $contents))
    {
      $this->throwError('Error writing to outfile "%s".', $outfile);
    }
    
    $this->verbose('Success! (output file "%s").', $outfile);
  }
  
  /**
   * Crawl from the given root path and look through all sub directories,
   * collect all resource files. 
   * 
   */
  private function crawl($path)
  {
    $files = array();

    $path = realpath($path);

    $handle = opendir($path);
    while (false !== ($file = readdir($handle)))
    {
      if ($file=='.' || $file=='..') {
        continue;
      }
      
      $fullname = $path . DIRECTORY_SEPARATOR . $file;
  
      if (is_dir($fullname)) {
        $files = array_merge($files, $this->crawl($fullname));
      }

      if ($this->isIncludedAsset($file)) {
        array_push($files, $this->fixUrlPathname($fullname));
      }
    }
  
    // tidy up: close the handler
    closedir($handle);
    
    return $files;
  }

  /**
   * Replace any Windows-style backslashes with slashes.
   * 
   * @param  string $path  Fully qualified path name
   * @return string
   */
  protected function fixUrlPathname($path)
  {
    return preg_replace('/[\/\\\]/', '/', $path);
  }


  /**
   * Returns true if the file is a revved resource.
   * 
   * The filename must match one of the INCLUDE_ASSETS patterns.
   * 
   * @param  string $file   Filename
   * @return boolean
   */
  private function isIncludedAsset($file)
  {
    foreach ($this->includeAssets as $pattern)
    {
      if (preg_match($pattern, $file) === 1) {
        return true;
      }
    }
    
    return false;
  }

  /**
   * Create the php file that can be included to have timestamp information
   * for all the revved files.
   * 
   * @return string    Content to save as a php file 
   */
  private function build($files)
  {
    $doc_file = __FILE__;

    date_default_timezone_set('UTC');
    $doc_time = date('Y-n-j G:i');
    
    // start buffering content of this file
    ob_start();
    
echo <<<EOD
<?php
/**
 * This file was generated by script "$doc_file"
 * 
 * @date    $doc_time
 */
EOD;

    echo "\nreturn array(\n";

    // format basepath of document root to substract from the resource urls
    $basePath = $this->fixUrlPathname(realpath($this->webPath));

    $assets = array();

    foreach ($files as $file)
    {
      $timestamp = filemtime($file);
      
      // use as array key, the resource url relative to the document root
      // (this should match the url passed to the stylesheet and javascript include helpers)
      $resourcePath = $this->getRelativePathFrom($file, $basePath);
      
      $assets[] = "'$resourcePath' => $timestamp";
    }
    
    echo implode(",\n", $assets) . "\n);";
    
    return ob_get_clean();
  }
  
  /**
   * Return a relative path from an absolute path, given the base path.
   * 
   * @param  string $path  Fully qualified source path, can include filename
   * @param  string $base  Fully qualified base path (no filename)
   * @return string  Relative path, without leading separator
   */
  private function getRelativePathFrom($path, $base)
  {
    $pos = strpos($path, $base);
    if ($pos === false || $pos !== 0) {
      $this->throwError('getRelativePathFrom() path (%s) does not start with base (%s)', $path, $base);
    }
    
    return substr($path, strlen($base));
  }

  private function showHelp()
  {
    echo <<<EOL
Usage: php build_app.php --webroot <path> --out <file>

  --webroot <path>  The web document root to crawl for resources (required)
  --out <file>      Output filename (required)
  -v                Verbose mode (optional)
   
EOL;
  }

  private function throwError()
  {
    $args = func_get_args();
    $message = call_user_func_array('sprintf', $args) . "\n";
    die($message);
  }
  
  private function verbose()
  {
    $args = func_get_args();
    if ($this->options['verbose']) {
      $message = call_user_func_array('sprintf', $args) . "\n";
      fwrite(STDERR, $message);
    }
  }
}

$tool = new BuildApp();
