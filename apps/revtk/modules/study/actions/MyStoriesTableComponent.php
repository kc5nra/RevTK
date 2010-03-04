<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * MyStoriesTable Component.
 * 
 * Just a quick rewrite of old code, REFACTOR if page is expanded.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class MyStoriesTableComponent extends coreComponent
{
	public function execute($request)
	{
		$queryParams = $this->getUser()->getLocalPrefs()
			->syncRequestParams('mystorieslist', array(
				uiSelectPager::QUERY_ROWSPERPAGE => 10
			));
		
		// pager
		$this->pager = new uiSelectPager(array
		(
		  'select'       => StoriesPeer::getMyStoriesSelect($this->getUser()->getUserId()),
	    'internal_uri' => 'study/mystories',
	    'query_params' => array(
			  uiSelectTable::QUERY_SORTCOLUMN => $request->getParameter(uiSelectTable::QUERY_SORTCOLUMN, 'framenum'),
        uiSelectTable::QUERY_SORTORDER  => $request->getParameter(uiSelectTable::QUERY_SORTORDER, 1),
				uiSelectPager::QUERY_ROWSPERPAGE => $queryParams[uiSelectPager::QUERY_ROWSPERPAGE]
	    ),
	    'max_per_page' => $queryParams[uiSelectPager::QUERY_ROWSPERPAGE],
	    'page'         => $request->getParameter(uiSelectPager::QUERY_PAGENUM, 1)
	  ));
		$this->pager->init();

		// order by
		$order_by = array(
			'framenum' => 'framenum ASC',
			'keyword'  => 'keyword ASC',
			'lastedit' => 'updated_on DESC',
			'votes'    => 'stars DESC'
		);
		$sortkey = $request->getParameter('sort', 'lastedit');
		$this->getController()->getActionInstance()->forward404Unless(!$sortkey || preg_match('/^[a-z]+$/', $sortkey));
		$orderClause = isset($order_by[$sortkey]) ? $order_by[$sortkey] : $order_by['framenum'];

		// get row data
		$get_select = clone($this->pager->getSelect());
		$get_select->order($orderClause);
		$rows = coreContext::getInstance()->getDatabase()->fetchAll($get_select);

		foreach ($rows as &$R)
		{
			$R['stars'] = $R['stars'] ? '<span class="star">'.$R['stars'].'</span>' : '<span>&nbsp;</span>';
			$R['kicks'] = $R['kicks'] ? '<span class="report">'.$R['kicks'].'</span>' : '<span>&nbsp;</span>';
			$R['story'] = StoriesPeer::getFormattedStory($R['story'], $R['keyword'], false);
		}

		$this->rows = $rows;

		return coreView::SUCCESS;
	}
}

