<?php
/**
 * CoreJs documentation.
 * 
 * @author     Fabrice Denis
 */

class corejsActions extends coreActions
{
  /**
   * Documentation : CoreJs
   * 
   * Main controller for all the coreJs framework documentation.
   * 
   *  /core.php/doc/corejs/ui/ajaxrequest
   * 
   * @return 
   */
  public function executeIndex($request)
  {
    $this->corejs_cat = $request->getParameter('cat');
    $this->corejs_subcat = $request->getParameter('subcat');
    
    $this->isIndex = $request->getParameter('cat') === 'index';
    
    if (!$this->isIndex) {
      $this->corejs_partial = $this->corejs_cat . '_' . $this->corejs_subcat;
    }
  }
  
  /**
   * Controllers for corejs demos.
   * 
   */
  
  // Ajaxrequest demo, test response
  public function executeAjaxtest()
  {
    return $this->renderPartial('ajaxtest');
  }

  // Ajaxrequest demo, JSON response
  public function executeJsontest()
  {
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json');
    $o = array(
      'foo' => 'bar'
    );
    return $this->renderText(coreJson::encode($o));
  }
  
  // uiAjaxPanel, demo 1
  public function executeUiajaxpaneldemo1()
  {
    return $this->renderComponent('corejs', 'demo1panel');
  }
  
  // uiAjaxPanel, demo 2
  public function executeUiajaxpaneldemo2()
  {
    return $this->renderPartial('corejs/demo2panel', array());
  }
  
  // AjaxTable demo
  public function executeAjaxtable($request)
  {
    return $this->renderPartial('ajaxtable');
  }
  // SelectionTable demo
  public function executeSelectiontable($request)
  {
    return $this->renderPartial('ajaxtable', array('selection' => true));
  }
  
  // uiModalDialog demo, ajax dialog
  public function executeAjaxmodaldialog($request)
  {
  // Test a 500 Internal Server error
  //  throw new coreException('Aouchee!');

  // Test a 404 Nout Found error
  //  $this->getResponse()->setStatusCode(404);
  //  return $this->renderText('');

  // Test a timeout error
  //  sleep(3);
    return $this->renderPartial('corejs/ajaxmodaldialog', array());
  }  
}
