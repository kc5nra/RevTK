<?php
/**
 * Form Helpers & Form Validation demo.
 * 
 * @package    Core
 * @subpackage Demos
 * @author     Fabrice Denis
 * @link     /demo.php/test/formdemo
 */

class formdemoAction extends coreAction
{
  public function execute($request)
  {
    if ($request->getMethod() == coreRequest::POST)
    {
      $validator = new coreValidator('formdemo');
      $validator->validate($request->getParameterHolder()->getAll());
    }
  }

  public function formdemoValidate()
  {
    
  }
}
