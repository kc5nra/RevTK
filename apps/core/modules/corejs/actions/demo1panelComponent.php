<?php
/**
 * demo1panel Component.
 * 
 * @package Ui
 * @author  Fabrice Denis
 */

class demo1panelComponent extends coreComponent
{
  public function execute($request)
  {

    if ($request->getMethod()!==coreRequest::POST)
    {
      // Default state
      $request->setParameter('reset', 1);
    }
    else
    {
      $txtName = trim($request->getParameter('txtName', ''));
      
      if ($txtName=='')
      {
        $request->setError('oops', 'Please enter txtName.');
        return;
      }
    }


    return;
  }
}