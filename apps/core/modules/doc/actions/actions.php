<?php
/**
 * Documentation pages.
 * 
 * @package    Core
 * @subpackage apps/doc
 * @author     Fabrice Denis
 */

class docActions extends coreActions
{
	public function executeIndex()
	{
		return coreView::SUCCESS;
	}

	/**
	 * Documentation : Helper pages
	 * 
	 * @return 
	 */
	public function executeHelper()
	{
		// Make sure the url variable is lowercase as its used as a view name
		$helper_name = strtolower($this->getRequest()->getParameter('helper_name'));

		if ($helper_name=='Index')
			return coreView::SUCCESS;

		// Use a view based on what helper requested
		return $helper_name;
	}

	/**
	 * Documentation : Core pages
	 * 
	 * @return 
	 */
	public function executeCore()
	{
		$include_name = $this->getRequest()->getParameter('include_name');
		return $include_name;
	}

	/**
	 * Documentation : Miscellaneous pages
	 * 
	 * @return 
	 */
	public function executeMisc()
	{
		$page_id = $this->getRequest()->getParameter('page_id');
		return $page_id;
	}

	/**
	 * Documentation : Library pages
	 * 
	 * @return 
	 */
	public function executeLib()
	{
		$page_id = $this->getRequest()->getParameter('page_id');
		return $page_id;
	}
}
