<?php
/**
 * Example component, using one action per file.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 * @see  	documentation/helpers/partial
 */

class componentDemo1Component extends coreComponent
{
	public function execute()
	{
		$this->component_set_var = 'Error';

		if (isset($this->include_component_var)) {
			$this->component_set_var = 'Success';
		}

		$this->component_variables = $this->getVarHolder()->getAll();

		return coreView::SUCCESS;
	}
}