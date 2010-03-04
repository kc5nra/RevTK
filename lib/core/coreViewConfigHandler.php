<?php
/**
 * coreConfigHandler allows to load and merge configuration files written
 * in native php (associative arrays).
 * 
 * String keys are overwritten if they are not arrays, if they are, then 
 * the arrays are merged recursively. Numerical keys are appended, while string keys are
 * overwritten. This allows developer to define settings at different levels, eg. global and
 * specific, and obtain the resulting settings.
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

class coreConfigHandler
{
 	protected
	  $configValues = array();

	/**
	 * Filename extension for configration files.
	 */
	const CONFIG_FILE_EXT = '.config.php';
}


/**
 * Merge action view configuration with the app's default view configuration.
 * 
 * Then applies the configuration to the web response, without overwriting values that were
 * set directly in the web response by the action.
 * 
 * @package    Core
 * @subpackage View
 * @author     Fabrice Denis
 */
class coreViewConfigHandler extends coreConfigHandler
{
 	/**
 	 * Load the application-level view configuration default settings.
 	 * 
 	 */
 	public function __construct()
	{
		$this->configValues = coreConfig::get('default_view_configuration');
	}
	
	/**
	 * Merge the action's view configuration with the application's default view configuration.
	 * 
	 * @return 
	 */
	public function mergeConfig($moduleName, $actionName, $viewName)
	{
		$config = $this->fixForDeepMerge( $this->configValues );

		$configFile = coreConfig::get('app_module_dir').'/'.$moduleName.'/config/view'.self::CONFIG_FILE_EXT;
		
		if (is_readable($configFile))
		{
			$configValues = require($configFile);

			// php config file must return an array
			if(!is_array($configValues)) {
				throw new coreException('Error loading configuration file '.$configFile);
			}

			// the default view configuration for this module
			$allConfig = isset($configValues['all']) ? $configValues['all'] : null;
	
			// FIXME  Decide to keep the 'Success' suffix like Symfony, or loose it.
			if ($viewName===coreView::SUCCESS) {
				$viewName = '';
			}
	
			// this view's configuration values
			$viewKey = $actionName.$viewName;
			$viewConfig = isset($configValues[$viewKey]) ? $configValues[$viewKey] : null;

			if ($allConfig!==null)
			{
				$config = coreToolkit::arrayDeepMerge($config, $this->fixForDeepMerge($allConfig) );
			}
	
			if ($viewConfig!==null)
			{
				$config = coreToolkit::arrayDeepMerge($config, $this->fixForDeepMerge($viewConfig) );
			}
		}
		
		$this->configValues = $config;
	}
	
	/**
	 * Add " => array() " to the javascripts and stylesheets which have no options set.
	 * 
	 * Fixes something with arrayDeepMerge() where non-associative entries do not merge but
	 * replace, so for example a stylesheet at the view config level will replace another
	 * stylesheet at the settings.php level if both are defined without the options array.
	 * 
	 * @return array
	 */
	protected function fixForDeepMerge(array $config)
	{
		foreach (array_keys($config) as $key)
		{
			if ($key==='stylesheets' || $key==='javascripts')
			{
				foreach ($config[$key] as $array_key => $value)
				{
					if (is_numeric($array_key))
					{
						unset($config[$key][$array_key]);
						$config[$key][$value] = array();
					}
				}
			}
		}
		return $config;
	}
	
	/**
	 * Apply the view configuration to the web response,
	 * while keeping the values set by the action directly through coreWebResponse.
	 * 
	 * Config keys and Response method used:
	 * 
	 *   title                =>   setTitle()
	 *   metas [array]        =>   addMeta()
	 *   http_metas [array]   =>   addHttpMeta()
	 *   stylesheets [array]  =>   addStylesheet()
	 *   javascripts [array]  =>   addJavascript()
	 * 
	 * 
	 * @param coreView $viewInstance   View to apply configuration to
	 * @return 
	 */
	public function applyConfig()
	{
		$config = $this->configValues;
		$response = coreContext::getInstance()->getResponse();
		
		// apply view config layout, or default layout, unless explicitly set in action
		$controller = coreContext::getInstance()->getController();
		$layoutKey = 'core.view.'.$controller->getModuleName().'_'.$controller->getActionName().'_layout';
		if (coreConfig::get($layoutKey)===null && isset($config['layout']))
		{
			coreConfig::set($layoutKey, $config['layout']);
		}	

		// http meta tags
		if (isset($config['http_metas']))
		{
			foreach($config['http_metas'] as $key => $value)
			{
				$response->addHttpMeta($key, $value, false);
			}
		}

		// apply meta tags
		if (isset($config['metas']))
		{
			foreach($config['metas'] as $key => $value)
			{
				// add http-equiv meta tags
				if ($key=='content-type') {
					$response->addHttpMeta($key, $value, false);
				}
				// add other meta tags
				else {
					$response->addMeta($key, $value, false);
				}
			}
		}

		// apply document title
		if (isset($config['title']))
		{
			$response->setTitle($config['title'], false);
		}

		// apply stylesheets
		if (isset($config['stylesheets']))
		{
			foreach($config['stylesheets'] as $key => $value)
			{
				if (is_numeric($key))
				{
					// stylesheet is just a path
					$path = $value;
					$options = array();
					$position = '';
				}
				else {
					$path = $key;
					$options = $value;
					$position = '';
					if (isset($options['position'])) {
						$position = $options['position'];
						unset($options['position']);
					}
				}
				$response->addStylesheet($path, $position, $options);
			}
		}
		
		// apply javascripts
		if (isset($config['javascripts']))
		{
			foreach($config['javascripts'] as $key => $value)
			{
				if (is_numeric($key))
				{
					// just a path
					$path = $value;
					$options = array();
					$position = '';
				}
				else {
					$path = $key;
					$options = $value;
					$position = '';
					if (isset($options['position'])) {
						$position = $options['position'];
						unset($options['position']);
					}
				}

				$response->addJavascript($path, $position, $options);
			}
		}
		
		//echo '<pre><code>'.print_r($this->configValues, true).'</code></pre>';
		//return;
	}
}
