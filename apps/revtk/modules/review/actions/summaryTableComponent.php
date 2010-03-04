<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * summaryTable Component.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class summaryTableComponent extends coreComponent
{
	/**
	 * Component variables:
	 * 
	 *   ts_start   Timestamp from the review session start time
	 * 
	 * @param object $request
	 */
	public function execute($request)
	{
		$queryParams = $this->getUser()->getLocalPrefs()
			->syncRequestParams('reviewsummary', array(
				uiSelectPager::QUERY_ROWSPERPAGE => 20,
				uiSelectTable::QUERY_SORTCOLUMN  => 'framenum',
				uiSelectTable::QUERY_SORTORDER   => 0
			));

		// pager
		$this->pager = new uiSelectPager(array
		(
		  'select'       => ReviewsPeer::getReviewSessionFlashcards($this->getUser()->getUserId(), $this->ts_start),
	    'internal_uri' => '@review_summary',
	    'query_params' => $queryParams,
	    'max_per_page' => $queryParams[uiSelectPager::QUERY_ROWSPERPAGE],
	    'page'         => $request->getParameter(uiSelectPager::QUERY_PAGENUM, 1)
	  ));
		$this->pager->init();
		
		// data table
		$this->table = new uiSelectTable(new FlashcardListBinding(), $this->pager->getSelect(), $request->getParameterHolder());

		return coreView::SUCCESS;
	}
}

/**
 * Kanji Review Summary
 * 
 */
class FlashcardListBinding implements uiSelectTableBinding
{
	public function getConfig()
	{
		return <<< EOD
		{
			settings: {
				primaryKey: ['userid', 'framenum'],
				sortColumn:	'framenum',
				sortOrder:	0
			},
			columns: [
				{
					caption: 	'Framenum',
					width: 		5,
					cssClass:	'center',
					colData:	'framenum'
				},
				{
					caption: 	'Kanji',
					width: 		7,
					cssClass:	'kanji',
					colData:	'kanji',
					colDisplay: '_kanji'
				},
				{
					caption: 	'Keyword',
					width: 		19,
					cssClass:	'keyword left',
					colData:	'keyword',
					colDisplay:	'keyword'
				},
				{
					caption: 	'OnYomi',
					width: 		15,
					cssClass:	'nowrap',
					colData:	'onyomi'
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
					colData:	'failurecount'
				},
				{
					caption: 	'Box',
					width: 		8,
					cssClass:	'bold center',
					colData:	'leitnerbox'
				}
			]
		}
EOD;
	}

	public function filterDisplayData(uiSelectTableRow $row)
	{
		$rowData =& $row->getRowData();

		$rowData['failurecount'] = $rowData['failurecount']!=0 ? $rowData['failurecount'] : ''; 
		$rowData['_kanji'] = cjk_lang_ja($rowData['kanji']);
		$rowData['keyword'] = link_to_keyword($rowData['keyword'], $rowData['framenum']);
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
