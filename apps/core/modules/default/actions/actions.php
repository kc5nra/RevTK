<?php
/**
 * Index page for the Core framework test & documentation application.
 * 
 * @package Core
 * @author  Fabrice Denis
 */
 
class defaultActions extends coreActions
{
	public function executeIndex()
	{
		//$this->getContext()->getResponse()->setTitle('kramerzzz');

		return coreView::SUCCESS;
	}
	
	/**
	 * Default 404 Page, as configured in settings.php
	 * 
	 * @return 
	 */
	public function executeError404()
	{
	}
}
