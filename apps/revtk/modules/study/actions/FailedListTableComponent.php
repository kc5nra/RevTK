<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Failed Kanji List uiWidgets.AjaxTable Component.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class FailedListTableComponent extends coreComponent
{
	public function execute($request)
	{
		$queryParams = $this->getUser()->getLocalPrefs()
			->syncRequestParams('failedlist', array(
				uiSelectPager::QUERY_ROWSPERPAGE => 20,
				uiSelectTable::QUERY_SORTCOLUMN  => 'framenum',
				uiSelectTable::QUERY_SORTORDER   => 0
			));

		// pager
		$this->pager = new uiSelectPager(array
		(
		  'select'       => ReviewsPeer::getAllRestudyKanjiSelect($this->getUser()->getUserId()),
	    'internal_uri' => 'study/failedlist',
	    'query_params' => $queryParams,
	    'max_per_page' => $queryParams[uiSelectPager::QUERY_ROWSPERPAGE],
	    'page'         => $request->getParameter(uiSelectPager::QUERY_PAGENUM, 1)
	  ));
		$this->pager->init();
		
		// data table
		$this->table = new uiSelectTable(new FailedListTableBinding(), $this->pager->getSelect(), $request->getParameterHolder());
		$this->table->configure(array(
			'sortColumn' => $queryParams[uiSelectTable::QUERY_SORTCOLUMN],
			'sortOrder'  => $queryParams[uiSelectTable::QUERY_SORTORDER]
		));

	}
}

/**
 * Kanji Review Summary
 * 
 */
class FailedListTableBinding implements uiSelectTableBinding
{
	public function getConfig()
	{
		return <<< EOD
		{
			settings: {
				primaryKey: ['framenum']
			},
			columns: [
				{
					caption: 	'Framenum',
					width: 		5,
					cssClass:	'center',
					colData:	'framenum'
				},
				{
					caption: 	'Keyword',
					width: 		19,
					cssClass:	'keyword',
					colData:	'keyword',
					colDisplay:	'_keyword'
				},
				
				/* flashcard data */
				{
					caption: 	'Pass',
					width: 		8,
					cssClass:	'bold center',
					colData:	'successcount'
				},
				{
					caption: 	'Fail',
					width: 		8,
					cssClass:	'center red',
					colData:	'failurecount',
					colDisplay: '_failurecount'
				},
				{
					caption: 	'Last&nbsp;Review',
					width: 		15,
					cssClass:	'center nowrap',
					colData:	'ts_lastreview',
					colDisplay:'_lastreview'
				}
			]
		}
EOD;
	}

	public function filterDisplayData(uiSelectTableRow $row)
	{
		$rowData =& $row->getRowData();
		$rowData['_failurecount'] = $rowData['failurecount']==0 ? '' : $rowData['failurecount'];
		$rowData['_keyword'] = link_to_keyword($rowData['keyword'], $rowData['framenum']);
		$rowData['_lastreview'] = format_date((int)$rowData['ts_lastreview'], rtkLocale::DATE_SHORT);
	}
	
	public function validateRowData(array $rowData)
	{
	}
	
	public function saveRowData(array $rowData, $newrow = false)
	{
	}
	
	public function deleteRow(array $row_ids)
	{
	}
}

