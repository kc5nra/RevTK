<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Extend the response object with versioning of javascript and stylesheet files.
 * 
 * Javascript and stylesheet files use a different naming pattern with a version number,
 * which is redirected by the .htaccess file (mod_rewrite) to a php script.
 * The script returns js & css files compressed with expiry information in the headers.
 * 
 * @todo   Currently only works with use_stylesheet() and use_javascript() helpers (AssetHelper)
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class rtkWebResponse extends coreWebResponse
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
    $css = $this->getVersionUrl($css);
    parent::addStylesheet($css, $position = '', $options);
  }

  /**
   * Adds javascript code to the current web response.
   *
   * @see  coreWebResponse::addJavascript()
   */
  public function addJavascript($js, $position = '', $options = array())
  {
    $js = $this->getVersionUrl($js);
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
    if ($this->resourceVersion===null)
    {
      $this->resourceVersion = require_once(coreConfig::get('config_dir').'/versioning.inc.php');
    }
    return $this->resourceVersion;
  }

  /**
   * Adds a unique version identifier to the css and javascript file names,
   * (using the local file modified times from build script), to prevent client
   * browsers from using the cache when a css/js file is updated.
   * 
   * The .htaccess files redirects those "versioned" files to a php script that
   * will strip the version number to get the actual file, and return the file
   * gzipped if possible to minimized download size.
   * 
   * @param  string  $resource  Css or Javascript url
   * @return string  Resource url with version number in it
   */
  protected function getVersionUrl($url)
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
      $url = '/version/cached-resource.php?path='.urlencode($url);
    }
    else
    {
      $versions = $this->getResourceVersion();    
      $path = pathinfo($url);
      $ver = isset($versions[$url]) ? '_v'.$versions[$url] : '';
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
