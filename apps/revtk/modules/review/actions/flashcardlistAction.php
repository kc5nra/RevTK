<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 
 * 
 * @package    Core
 * @author     Fabrice Denis
 */

class flashcardlistAction extends coreAction
{
	public function execute($request)
	{
		$queryParams = $this->getUser()->getLocalPrefs()
			->syncRequestParams('detailedflashcardlist', array(
				uiSelectPager::QUERY_ROWSPERPAGE => 20,
				uiSelectTable::QUERY_SORTCOLUMN  => 'framenum',
				uiSelectTable::QUERY_SORTORDER   => 0
			));

		$this->pager = new uiSelectPager(array
		(
		  'select'       => ReviewsPeer::getDetailedFlashcardList($this->getUser()->getUserId()),
	    'internal_uri' => 'review/flashcardlist',
	    'query_params' => $queryParams,
	    'max_per_page' => $queryParams[uiSelectPager::QUERY_ROWSPERPAGE],
	    'page'         => $request->getParameter(uiSelectPager::QUERY_PAGENUM, 1)
	  ));
		$this->pager->init();
		
		$this->table = new uiSelectTable(new FlashcardListBinding(), $this->pager->getSelect(), $request->getParameterHolder());
		$this->table->configure(array(
			'sortColumn' => $queryParams[uiSelectTable::QUERY_SORTCOLUMN],
			'sortOrder'  => $queryParams[uiSelectTable::QUERY_SORTORDER]
		));
		
	}
}

/**
 * Detailed Flashcard List
 * 
 */
class FlashcardListBinding implements uiSelectTableBinding
{
	public function getConfig()
	{
		coreToolkit::loadHelpers(array('CJK'));
		
		return <<< EOD
		{
			settings: {
				primaryKey: ['userid', 'framenum']
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
					colDisplay:	'_kanji'
				},
				{
					caption: 	'Keyword',
					width: 		19,
					cssClass:	'keyword',
					colData:	'keyword',
					colDisplay:	'_keyword'
				},
				{
					caption: 	'OnYomi',
					width: 		15,
					cssClass:	'nowrap',
					colData:	'onyomi'
				},
				{
          caption:  'StrokeCount',
          width:    15,
          cssClass: 'center',
          colData:  'strokecount'
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
				},
				{
					caption: 	'Last&nbsp;Review',
					width: 		15,
					cssClass:	'center',
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

		if ($rowData['failurecount']==0)
		{
			$rowData['failurecount'] = '';
		}
		$rowData['_kanji'] = cjk_lang_ja($rowData['kanji']);
		$rowData['_keyword'] = link_to_keyword($rowData['keyword'], $rowData['framenum']);
		
		$lastReviewTS = (int)$rowData['ts_lastreview'];
		
		$rowData['_lastreview'] = $lastReviewTS ? format_date($lastReviewTS, rtkLocale::DATE_SHORT) : '-';
		
		return $rowData;
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
