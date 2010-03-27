<?php
/**
 * The test module contains miscellaneous tests of the framework features.
 * 
 * The tests are not linked in the documentation webpage, but each action below
 * is easily accessed by url like this:
 * 
 * http://kanji.koohii.refactor/demo.php/test/<actionname>
 * 
 * 
 * @package    Core
 * @subpackage apps/test
 * @author     Fabrice Denis
 */

class testActions extends coreActions
{
  /**
   * Layout documentation page
   * 
   * @return 
   */
  public function executeLayout($request)
  {
    if ($layout_name = $request->getParameter('layout_name') !== 'default') {
      $this->setLayout('other_layout');
    }

    return coreView::SUCCESS;
  }

  /**
   * Returns a text/plain page.
   * 
   * @return 
   */
  public function executePlaintext()
  {
    $this->getResponse()->setContentType('text/plain');

    return coreView::NONE;
  }

  /**
   * Returns a application/json snippet.
   * 
   * @return 
   */
  public function executeJson()
  {
    $this->setLayout(false);

    $this->getResponse()->setContentType('application/json');

    return coreView::SUCCESS;
  }

  /**
   * Action that returns headers only.
   * For example sending X-JSON for Prototype.
   * 
   * @return 
   */
  public function executeHeadersonly()
  {
      $output = '{"title": "My basic letter", "name": "Mr Brown"}';
        $this->getResponse()->setHttpHeader("X-JSON", '('.$output.')');

    return coreView::NONE;
  }


  /**
   * Bypassing the view layer and set the HTML code directly from the action.
   * 
   * @return 
   */
  public function executeSkipview()
  {
    $this->setLayout(false);

    return $this->renderText("<html><body>Hello, World!</body></html>");
  }

  /**
   * Output escaping test.
   * 
   * @return 
   */
  public function executeOutputescaping()
  {
    $this->dangerous_message = '<script>alert(document.cookie)</script>';
    
    return coreView::SUCCESS;
  }

  /**
   * Simple template with variables.
   */
  public function executeDemo1()
  {
    $this->var1 = 'Jack Vance';
    $this->var2 = 'Lorem Ipsum Book Title';
    
    return coreView::SUCCESS;
  }
  
  /**
   * Action using a different template by returning a custom action termination.
   * 
   * View template name = action name + action termination, here 'demo2Error'
   * 
   */
  public function executeDemo2()
  {
    return 'Error';
  }
  
  /**
   * Various database access tests, see the template file.
   * 
   */
  public function executeDatabase()
  {
  }

  /**
   * coreUser demo.
   * 
   * @link /demo.php/test/user
   */
  public function executeUser()
  {
  }

  /**
   * Security demo : authentication & credentials.
   * 
   * Also demonstrates extending the core user class (see "core_factories" in settings.php).
   * 
   * @link /demo.php/test/security
   */
  public function executeSecuritydemo($request)
  {
    if ($request->hasParameter('btnSigninAdmin'))
    {
      $this->getUser()->signInDemo('Administrator', array('member', 'admin'));
    }
    elseif ($request->hasParameter('btnSigninMember'))
    {
      $this->getUser()->signInDemo('Member', 'member');
    }
    elseif ($request->hasParameter('btnSignout'))
    {
      $this->getUser()->signOutDemo();
    }
    
    if (!$this->getUser()->isAuthenticated())
    {
      $this->getUser()->setAttribute('name', 'Guest');
    }
  }

  /**
   * This page requires admin credentials, see security config file.
   * 
   * Verify that the user is forwarded to the "secure" page it not having "admin" credential.
   * 
   * @return 
   * @param object $request
   */
  public function executeSecurityadmin()
  {
  }

  /**
   * Default "login" action example (as defined in settings.php)
   * 
   */
  public function executeSecuritylogin()
  {
  }

  /**
   * Default "secure" action example (as defined in settings.php)
   * 
   */
  public function executeSecuritysecure()
  {
  }
  
  /**
   * Demonstrates forward404().
   * 
   */
  public function executeForward404()
  {
    $this->forward404();

    return $this->renderText("Not forwarded");
  }

  /**
   * Test the default URL Route "module/action/*"
   * eg. http:// ... /test/params/show/id/2
   */
  public function executeParams()
  {
  }
  
  /**
   * Test Cookies.
   * 
   * @link  /test/cookies
   * 
   * @return 
   */
  public function executeCookies()
  {
  }

  /**
   * Test Exception.
   * 
   * @link  /test/exception
   * 
   * @return 
   */
  public function executeException()
  {
    // test custom exception
    require(coreConfig::get('root_dir').'/apps/revtk/lib/rtkAjaxException.php');
    
    throw new rtkAjaxException('bleh');
  }
  
  /**
   * Test renderPartial()
   * 
   * @link /test/renderPartial
   */
  public function executeRenderPartial()
  {
    // var1 is set, var2 will be undefined
    $this->var1 = 'Variable One';

    return $this->renderPartial('test/demo1View');
  }

  /**
   * Test renderComponent()
   * 
   * @link /test/renderPartial
   */
  public function executeRenderComponent()
  {
    // var1 is set
    $this->param1 = 'Parameter One';

    return $this->renderComponent('test', 'demo1');
  }

  /**
   * Test Url Encoding of Query String Variables
   * 
   * @return 
   */
  public function executeUrlEncoding($request)
  {
    // redirect with get variable
    if ($request->getMethod()===coreRequest::POST)
    {
      $vars = array('variable' => $request->getParameter('variable'));
      $this->redirect('test/urlencoding?'.http_build_query($vars));
    }

  }
}
