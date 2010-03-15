<?php
/**
 * Outputs resource with gzip compression and "never modified" cache headers.
 * 
 * This is meant only for some resources (js, css) which are also "versioned"
 * with a unique url every time the file is modified, the version is removed from the url
 * by .htaccess 's RewriteRule.
 * 
 * See /batch/build_app.php  which outputs the version strings in /config/versioning.inc.php
 * based on file modified stamps.
 * 
 * Note: the file could be included and $variables substituted in Javascript/Css too.
 * 
 * @package Core
 * @author  Fabrice Denis
 * 
 */

// relative path from this script to the /web root folder where css/js resources are
define('RELATIVE_PATH_TO_ROOT', '../.');

define('USE_GZIP_ENCODING', 1);

function go_404($errormsg)
{
  global $filepath;
//  header("HTTP/1.0 404 File not found");
  echo('Go 404 because : '.$errormsg.' ('.RELATIVE_PATH_TO_ROOT.$filepath.')');
  exit;
}

  $filepath = isset($_GET[path]) ? $_GET[path] : '';
  
  if ($filepath==''){
    echo 'Error.';
    exit();
  }

  # on web server the path doesn't come with a trailing slash, go figure
  if (strpos($filepath, '/')!==0){
    $filepath = '/'.$filepath;
  }


  header("Expires: ".gmdate("D, d M Y H:i:s", time()+315360000)." GMT");
  header("Cache-Control: max-age=315360000");
  
  
  # ignore paths with a '..'
  if (preg_match('!\.\.!', $filepath)){ go_404('error1'); }
  
  # make sure our path starts with a known directory
  #if (!preg_match('!^/*(js|css)!', $filepath)){ go_404('error2'); }

/* TODO : concatenate files
  if ($filepath=='/study/study.js')
  {
    exit();
  }
*/


  # does the file exist?
  if (!file_exists(RELATIVE_PATH_TO_ROOT.$filepath)){ go_404('error3'); }
  
  # output a mediatype header
  $ext = array_pop(explode('.', RELATIVE_PATH_TO_ROOT.$filepath));
  switch ($ext)
  {
    case 'css':
      header("Content-type: text/css");
      break;
    
    case 'js' :
      header("Content-type: text/javascript");
      break;
    
    // script is currently called only for js and css files!  
    default:
      go_404('error4');
      break;
/*    
    case 'gif':
      header("Content-type: image/gif");
      break;
    case 'jpg':
      header("Content-type: image/jpeg");
      break;
    case 'png':
      header("Content-type: image/png");
      break;
    default:
      header("Content-type: text/plain");
*/
  }

  // don't use gzip compression on IE6 SP1 (hotfix  http://support.microsoft.com/default.aspx?scid=kb;en-us;823386&Product=ie600)
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $IE6bug = (strpos($ua, 'MSIE 6') && strpos($ua, 'Opera') == -1);

  // For some very odd reason, "Norton Internet Security" unsets this
  $_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';

  if (USE_GZIP_ENCODING && !$IE6bug && extension_loaded('zlib') && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false))
    ob_start('ob_gzhandler');
  else
    ob_start();

  # echo the file's contents
  //echo implode('', file(RELATIVE_PATH_TO_ROOT.$filepath));
  
  echo file_get_contents(RELATIVE_PATH_TO_ROOT.$filepath);

  ob_end_flush();
