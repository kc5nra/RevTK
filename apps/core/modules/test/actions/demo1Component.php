<?php
/**
 * Test renderComponent, using the demo1 view template.
 * 
 * @link    text/renderComponent
 * 
 * @author  Fabrice Denis
 */

class demo1Component extends coreComponent
{
	public function execute($request)
	{
		$this->var1 = "Received param1 = " . $this->param1;

		return coreView::SUCCESS;
	}
}