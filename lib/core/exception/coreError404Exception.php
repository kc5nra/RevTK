<?php
/**
 * This exception is thrown when a 404 error occurs in an action.
 * In production environment, it will forward the request to the default 404 action configured in settings.php.
 * 
 * @package    Core
 * @subpackage Exception
 * @author     Fabrice Denis
 * @copyright  Based on Symfony sfException class, by Fabien Potencier (www.symfony-project.org)
 */

class coreError404Exception extends coreException
{
  public function printStackTrace()
  {
    $exception = is_null($this->wrappedException) ? $this : $this->wrappedException;

    if (coreConfig::get('sf_debug'))
    {
      $response = coreContext::getInstance()->getResponse();
      $response->setStatusCode(404);

      return parent::printStackTrace();
    }
    else
    {
    // debug message
    //echo $exception->getMessage();
      coreContext::getInstance()->getController()->forward(coreConfig::get('error_404_module'), coreConfig::get('error_404_action'));
    }
  }
}

/**
 * Exception used by the Symfony classes (url routing mostly)
 */
class sfError404Exception extends coreError404Exception {}
