<?php
/**
 * Helpers to include user interface elements in the application templates.
 * 
 * Uses stylesheet /css/ui/widgets.css
 * 
 * @package  Helpers
 * @author   Fabrice Denis
 */

/**
 * Returns HTML for a uiFilterStd widget.
 * 
 * The links is an array of link definitions, in the format of the link_to() arguments:
 *   array(
 *     array($name, $internal_uri, $options),
 *     array($name, $internal_uri, $options),
 *     //...
 *   )
 *   
 * Options can be used to add attributes to the main div tag.
 * 
 * Add option 'active' as an integer to specify the active item: 0=first, 1=second, etc.
 * 
 * @param  string  $label     Label, pass empty string to display switches without label
 * @param  array   $links     Array of link definitions (array(name, internal_uri, options))
 * @param  array   $options   Options for the main div tag (class uiFilterStd will always be addded) 
 *                            Also, the 'active' option (see above)
 * 
 * @return string  HTML representation
 */
function ui_filter_std($label, $links, $options = array())
{
	// always set the widget class name in the main div tag
	$options['class'] = _merge_class_names(isset($options['class']) ? $options['class'] : array(), array('uiFilterStd'));

	if (isset($options['active']))
	{
		$active = (int)$options['active'];
		if ($active < count($links))
		{
			// add the active class
			$linkOptions = isset($links[$active][2]) ? $links[$active][2] : array();
			$linkOptions['class'] = (isset($linkOptions['class']) ? $linkOptions['class'] : '') . ' active';
			$links[$active][2] = $linkOptions;
		}
		unset($options['active']);
	}

	$view = new coreView(coreContext::getInstance());
	$view->getParameterHolder()->add(array('links' => $links, 'label' => $label, 'options' => $options));
	$view->setTemplate(dirname(__FILE__).'/templates/ui_filter_std.php');
	return $view->render();
}


/**
 * Set a slot and returns the HTML for a uiSelectPager.
 * 
 * The slot allows to re-print the pager at the top and bottom of a table by
 * running the pager template only once.
 * 
 * Examples:
 * 
 *  echo ui_select_pager($pager)
 *    => Set and print the pager slot with the default slot name
 *  echo ui_select_pager()
 *    => Print the HTML for previously set slot
 *  echo ui_select_pager($pager, 'pager2')
 *    => Print and set a pager with a custom slot name
 *       (allows different pagers on one template)
 *  echo ui_select_pager(false, 'pager2')
 *    => Print previously set pager with custom slot name
 * 
 * @param  mixed   $pager   uiSelectPager object or false
 * @param  string  $slot    Slot name, leave out to use the default
 * 
 * @return string  HTML representation
 */
function ui_select_pager($pager = false, $slot = 'widgets.ui.pager')
{
	if ($pager !== false)
	{
		slot($slot);
	
		$view = new coreView(coreContext::getInstance());
		$view->getParameterHolder()->add(array('pager' => $pager));
		$view->setTemplate(dirname(__FILE__).'/templates/ui_select_pager.php');
		echo $view->render();
	
		end_slot();
	}

	return get_slot($slot);
}


/**
 * Return HTML for a uiSelectTable component.
 * 
 * Optionally, returns the HTML for a uiSelectPager at the top and bottom of the table.
 * 
 * @param  uiSelectTable $table
 * @param  uiSelectPager $pager   Optional pager, to display paging links and rows-per-page
 * 
 * @return string  HTML representation
 */
function ui_select_table(uiSelectTable $table, uiSelectPager $pager = null)
{
	ob_start();

	if (!is_null($pager))
	{
		echo ui_select_pager($pager);
	}

	$view = new coreView(coreContext::getInstance());
	$view->getParameterHolder()->add(array('table' => $table));
	$view->setTemplate(dirname(__FILE__).'/templates/ui_select_table.php');
	echo $view->render();
	
	if (!is_null($pager))
	{
		echo ui_select_pager();
	}

	return ob_get_clean();
}


/**
 * uiDataFilter helper.
 * 
 * Links is an associative array of link key => definition,
 * the parameters for each link are passed directly to the link_to() helper,
 * so it supports internal uris, query_string option, ids and other attributes can be set.
 * 
 * To set no active link, and disable the default active link, set it to FALSE.
 * 
 * Only the link name (link text) is required. 'internal_uri' defaults to '#', and
 * 'options' defautls to an empty array.
 * 
 * The link parameter internal_uri defaults to '#' if not set.
 *
 * array(
 *   'link_key' => array(
 *     'name'         => 'Link Text',
 *     'internal_uri' => '@view_by_votes',
 *     'options'      => array('id' => 'view_by_botes')
 *   ),
 *   ....
 * )
 * 
 * The first link is always active by default, unless an id is given.
 * 
 * @param  string  $caption      The label on the left side next to the links, eg: "View:"
 * @param  array   $links        An array of link definitions
 * @param  string  $active_link  The key for the active link in the $links array
 * @return string  Html code
 */
function ui_data_filter($caption, $links, $active_link = '')
{
	// set first link active by default
	if ($active_link === '')
	{
		$keys = array_keys($links);
		$active_link = $keys[0];
	}

	// set defaults and active link class
	foreach ($links as $key => &$link)
	{
		if (!isset($link['internal_uri'])) {
			$link['internal_uri'] = '#';
		}
		if (!isset($link['options'])) {
			$link['options'] = array();
		}

		// set the active class on the active link
		$link['active'] = $key===$active_link;
		
		/*
		if ($key===$active_link)
		{
			$options = $link['options'];
			$options['class'] = _merge_class_names(isset($options['class']) ? $options['class'] : array(), 'active');
			$link['options'] = $options;
		}
		*/
	}

	$view = new coreView(coreContext::getInstance());
	$view->getParameterHolder()->add(array('caption' => $caption, 'links' => $links, 'active_link' => $active_link));
	$view->setTemplate(dirname(__FILE__).'/templates/ui_data_filter.php');
	return $view->render();
}


/**
 * Return html structure (to echo) for tabs in the manner of "sliding doors".
 *
 * SPANs are used because they can be styled on the :hover state in IE6
 * ( a:hover span {...} ).
 * 
 * Structure:
 * 
 *   <div class="ui-tabs" id="custom-id">
 *     <ul>
 *       <li><a href="#"><span>Link text</span></a></li>
 *       ...
 *     </ul>
 *     <div class="clear"></div>
 *   </div>
 * 
 * 
 * The $links argument is declared like this:
 * 
 *   array(
 *     array($name, $internal_uri, $options),
 *     array($name, $internal_uri, $options),
 *     ...
 *   )
 *   
 * The tab definitions are identical to the link_to() helper:
 *   
 *   $name          Label for the tab
 *   $internal_uri  Internal uri, or absolute url, defaults to '#' if empty (optional)
 *   $options       Html attribute options (optional)
 *   
 * By default the first tab is set active (class "active" on the LI tag). Specify the
 * index of the tab to be active, or FALSE to not add an "active" class.
 * 
 * @see    http://www.alistapart.com/articles/slidingdoors/
 * 
 * @param  array   $links    An array of tab definitions (see above).
 * @param  mixed   $active   Index of the active tab, defaults to the first tab.
 *                           Use FALSE to explicitly set no active tab (or use your own class).
 * @param  array   $options  Options for the container DIV element. By default the class "ui-tabs"
 *                           is added. Add "uiTabs" class for defaults styles, id for the javascript component.
 * 
 * @return string  Html code
 */
function ui_tabs($tabs, $active = 0, $options = array())
{
	ob_start();

	// add the "ui-tabs" class name
	$options['class'] = _merge_class_names(isset($options['class']) ? $options['class'] : array(), array('ui-tabs'));
  echo tag('div', $options, true) . "\n<ul>\n";

	$tab_index = 0;
	foreach ($tabs as $tab)
	{
		$name = '<span>'.$tab[0].'</span>';
		$internal_uri = isset($tab[1]) ? $tab[1] : '#';
		$options = isset($tab[2]) ? $tab[2] : array();

		$class_active = (is_int($active) && $active===$tab_index) ? ' class="active"' : '';
		echo '<li'.$class_active.'>'.link_to($name, $internal_uri, $options).'</li>'."\n";
		
		$tab_index++;
	}
	
	echo "</ul>\n<div class=\"clear\"></div>\n</div>\n";
	
	return ob_get_clean();
}


/**
 * Helper to set the display property inline stlye in html templates.
 * 
 * Example:
 *   <div ... style="<3php echo ui_display($active===3) 3>">
 * 
 * Echoes the display property with ending ";"
 */
function ui_display($bDisplay)
{
	echo $bDisplay ? 'display:block;' : 'display:none;';
}

/**
 * Start buffering contents of a rounded box.
 *
 * @param  string  $box_class  uiBox class, style for the ui box.
 */
function ui_box_rounded($box_class = 'uiBoxRDefault')
{
	coreConfig::set('helpers.ui.box', $box_class);
	ob_start();
	ob_implicit_flush(0);
}

/**
 * Stops buffering the contents of a rounded box,
 * returns the HTML code for the box and its contents.
 *
 * @return string  HTML code
 */
function end_ui_box()
{
	$content = ob_get_clean();
	$classname = coreConfig::get('helpers.ui.box', '');

	$view = new coreView(coreContext::getInstance());
	$view->getParameterHolder()->add(array('contents' => $content, 'class' => $classname));
	$view->setTemplate(dirname(__FILE__).'/templates/ui_box_rounded.php');
	return $view->render();
}


/**
 * Returns a uiIBtn element.
 * 
 * The parameters are the same as for UrlHelper link_to().
 * The difference is an additional "type" option, and an empty uri will default to '#'.
 *
 * Example markup:
 *
 *  <code>
 *   <a href="#" class="uiIBtn uiIBtnDefault"><span><em class="icon icon-edit">Edit</em></span></a>
 *  </code>
 * 
 * Additional options:
 * 
 *  'type'     The type of button, defaults to "uiIBtnDefault". This sets the main class
 *             of the uiIBtn element.
 *  'icon'     Adds an EM element inside the SPAN, with classname "icon icon-XYZ" where XYZ
 *             is the given icon name. 
 * 
 * Examples:
 *  
 *   echo ui_ibtn('Go');
 *   echo ui_ibtn('Disabled', '#', array('type' => 'uiIBtnDisabled'));
 *   echo ui_ibtn('Custom class', '#', array('class' => 'JsAction-something'));
 *   echo ui_ibtn('Google', 'http://www.google.com' );
 *   echo ui_ibtn('Click me!', '#', array('onclick' => 'alert("Hello world!");return false;') );
 * 
 * @param  string  $name          Button text can contain HTML (eg. <span>), will NOT be escaped
 * @param  string  $internal_uri  See link_to()
 * @param  array   $options       See link_to()
 * @return string
 */
function ui_ibtn($name, $internal_uri = '', $options = array())
{
	$button_type = 'uiIBtnDefault';
	
	if (isset($options['type']))
	{
		$button_type = $options['type'];
		unset($options['type']);
	}

	$options['class'] = _merge_class_names(isset($options['class']) ? $options['class'] : array(), array('uiIBtn', $button_type));

  if (isset($options['icon']))
  {
  	$name = '<em class="icon icon-'.$options['icon'].'">'.$name.'</em>';
    unset($options['icon']);
  }

  $name = '<span>'.$name.'</span>';  	

	if ($internal_uri == '') {
		$internal_uri = '#';
	}

	if ($internal_uri == '#') {
		$options['absolute'] = true;
	}

	return link_to($name, $internal_uri, $options);
}


/**
 * Returns HTML code for a uiWindow.
 * 
 * @uses   jquery, jquery UI/Draggable
 * @uses   ui_box_rounded() for the window border
 * 
 * @param  string  $content  HTML content for the uiWindow div.window-body
 * @param  array   $options  Tag attributes as for tag() helper (to set id, etc)
 *                           Classes can be set, "uiWindow" class is merged
 * 
 * @return string  HTML code
 */
function ui_window($content = '', $options = array())
{
	// add uiWindow class
	$options['class'] = _merge_class_names(isset($options['class']) ? $options['class'] : array(), array('uiWindow'));
	
	$view = new coreView(coreContext::getInstance());
	$view->getParameterHolder()->add(array('content' => $content, 'options' => $options));
	$view->setTemplate(dirname(__FILE__).'/templates/ui_window.php');
	return $view->render();
}


/**
 * Merge class names given as strings or arrays (array arguments is faster).
 * 
 * @param  mixed  $classnames      Class name(s) given as a "class" attribute string, or an array of class names
 * @param  mixed  $add_classnames  Class names to add, given as string or array
 * @return string   Css "class" attribute string with all class names combined
 */
function _merge_class_names($classnames, $add_classnames)
{
	if (is_string($classnames)) {
		$classnames = preg_split('/\s+/', $classnames);
	}
	
	if (is_string($add_classnames)) {
		$add_classnames = preg_split('/\s+/', $add_classnames);
	}
	
	if (count($add_classnames)) {
		$classnames = array_merge($classnames, $add_classnames);
	}

	return implode(' ', $classnames);
}

