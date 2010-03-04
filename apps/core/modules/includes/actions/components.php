<?php
/**
 * Example for multiple component actions into one file.
 * 
 * @package Core
 * @author  Fabrice Denis
 * @see  	documentation/helpers/partial
 */

class includesComponents extends coreComponents
{
	/**
	 * Test component action
	 * 
	 * @return 
	 */
	public function executeComponentDemo2()
	{
		$this->component_set_var = 'Error';

		if (isset($this->include_component_var)) {
			$this->component_set_var = 'Success';
		}

		$this->component_variables = $this->getVarHolder()->getAll();

		return coreView::SUCCESS;
	}

	/**
	 * Test component action
	 * 
	 * @return 
	 */
	public function executeComponentDemo3()
	{
		$this->component_set_var = 'Error';

		if (isset($this->include_component_var)) {
			$this->component_set_var = 'Success';
		}

		$this->component_variables = $this->getVarHolder()->getAll();

		return coreView::SUCCESS;
	}
}