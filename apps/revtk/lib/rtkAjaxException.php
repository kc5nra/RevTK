<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Exception thrown by ajax actions.
 *
 * During development, sends back the printout of the json data as a php object,
 * to verify the data integrity.
 * 
 * All json requests should use a "json" POST variable with the json data as a string.
 * 
 * @package    RevTK
 * @subpackage Exception
 * @author     Fabrice Denis
 */

class rtkAjaxException extends coreException
{
  public function printStackTrace()
  {
    $exception = is_null($this->wrappedException) ? $this : $this->wrappedException;
    $message   = $exception->getMessage();

    $response = coreContext::getInstance()->getResponse();
    $response->setStatusCode(500);

    // clean current output buffer
    while (@ob_end_clean());
    
    ob_start(coreConfig::get('sf_compressed') ? 'ob_gzhandler' : '');
    
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/plain');

    if ($message!=='') {
      header('RTK-Error: ' . $message);
    }

    // during development, send back ajax request for debugging
    if (coreConfig::get('sf_debug'))
    {
      try
      {
        $request = coreContext::getInstance()->getRequest();
        $sJson = $request->getParameter('json');
        $oJson = null;
        if ($sJson !== null) {
          $oJson = coreJson::decode($sJson);
        }
        
        echo 'Json data = '."\n". ($oJson!==null ? print_r($oJson, true) : $sJson );
      }
      catch (Exception $e)
      {
        echo 'rtkAjaxException - no Json found, $_POST = '."\n".print_r($_POST, true);
      }
    }

    exit(1);
  }
}
