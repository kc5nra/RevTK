<?php
/**
 * Extend the response object with versioning of javascript and stylesheet files.
 * 
 * How it works:
 * - In DEVELOPMENT environment, the include tags return the relative url to the version script,
 *   including parameters that point to the resource. The file is returned gzipped, with expires
 *   headers.
 * - In PRODUCTION environment, the include tags return a revved resource filename, using the
 *   file modified timestamp. Coupled with the far future expire header, this ensures maximum
 *   caching by the client, as well as refreshing the resource whenever the file is modified
 *   and re-deployed.
 *   
 * @link    http://particletree.com/notebook/automatically-version-your-css-and-javascript-files/
 * 
 * @package Core
 * @author  Fabrice Denis
 */

class demoWebResponse extends coreWebResponse
{
  protected
    $resourceVersion = null;
  
  /**
   * Adds a stylesheet to the current web response.
   * 
   * @see  coreWebResponse::addStylesheet()
   */
  public function addStylesheet($css, $position = '', $options = array())
  {
    $css = $this->getRevvedResourceUrl($css);
    parent::addStylesheet($css, $position = '', $options);
  }

  /**
   * Adds javascript code to the current web response.
   *
   * @see  coreWebResponse::addJavascript()
   */
  public function addJavascript($js, $position = '', $options = array())
  {
    $js = $this->getRevvedResourceUrl($js);
    parent::addJavascript($js, $position = '', $options);
  }

  /**
   * Return the resource version data, which contains the latest
   * version number (file modified time) for the css and js files in the project.
   * 
   * @return array
   */
  protected function getResourceVersion()
  {
    if ($this->resourceVersion === null)
    {
      $this->resourceVersion = require_once(coreConfig::get('config_dir').'/versioning.inc.php');
    }
    return $this->resourceVersion;
  }

  /**
   * Returns a revved resource url. 
   * 
   * The .htaccess files redirects those "versioned" files to a php script that
   * will strip the version number to get the actual file, and return the file
   * gzipped if possible to minimized download size.
   * 
   * @param  string  $resource  Css or Javascript url
   * @return string  Resource url with version number in it
   */
  protected function getRevvedResourceUrl($url)
  {
    // leave absolute URLs (usually from CDNs like Google and Yahoo) unchanged
    if (stripos($url, 'http:')===0)
    {
      return $url;
    }
    
    // do not use minified javascripts in debug environment
    if (coreConfig::get('sf_debug'))
    {
      $url = preg_replace('/\\.min\\.js/', '.js', $url);
    }

    if (coreConfig::get('sf_debug'))
    {
      // in development environment, show the url called by mod_rewrite
      $url = '/version/cache.php?env='.CORE_ENVIRONMENT.'&app='.CORE_APP.'&path='.urlencode($url);
    }
    else
    {
      $versions = $this->getResourceVersion();    

      $path = pathinfo($url);
      if (isset($versions[$url])) {
        $ver =  '_v' . $versions[$url];
      }
      else {
        // warn so we know that version number file need to be rebuilt
        // throw doesn't seem to work here, this is for staging anyway
        die('No version information for ' . $url);
      }
 
      preg_match('/(.+)(\\.[a-z]+)/', $path['basename'], $matches);
      $url =  $path['dirname'] . '/' . $matches[1] . $ver . $matches[2];
    }
    return $url;
  }
  
  /**
   * Sets response headers for a tetx response that can be saved
   * as a file by the user. This is useful for exporting data. 
   * 
   * @param string  $fileName  Filename proposed by the browser when the text response is returned
   */
  public function setFileAttachmentHeaders($fileName)
  {
    // set text mode for the data export
    $this->setContentType('text/plain; charset=utf-8');
    
    // disable cache and set file attachment name
    $this->setHttpHeader('Cache-Control', 'no-cache, must-revalidate');
    $this->setHttpHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
    $this->setHttpHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"');
  }
}
