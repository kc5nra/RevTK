<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Remove Flashcards
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class RemoveListTableComponent extends coreComponent
{
  public function execute($request)
  {
    $queryParams = $this->getUser()->getLocalPrefs()
      ->syncRequestParams('manage.removelist', array(
        uiSelectPager::QUERY_ROWSPERPAGE => 20,
        uiSelectTable::QUERY_SORTCOLUMN  => 'framenum',
        uiSelectTable::QUERY_SORTORDER   => 0
      ));
    
    // pager
    $this->pager = new uiSelectPager(array
    (
      'select'       => ReviewsPeer::getDetailedFlashcardList($this->getUser()->getUserId()),
      'internal_uri' => 'manage/removeListTable',
      'query_params' => $queryParams,
      'max_per_page' => $queryParams[uiSelectPager::QUERY_ROWSPERPAGE],
      'page'         => $request->getParameter(uiSelectPager::QUERY_PAGENUM, 1)
    ));
    $this->pager->init();
    
    // data table
    $binding = new RemoveListTableBinding();
    $this->table = new uiSelectTable($binding, $this->pager->getSelect(), $request->getParameterHolder());
    $this->table->configure(array(
      'sortColumn' => $queryParams[uiSelectTable::QUERY_SORTCOLUMN],
      'sortOrder'  => $queryParams[uiSelectTable::QUERY_SORTORDER]
    ));

  }
}

/**
 * Remove flashcards selection table, checkboxes allow to select flashcards to remove from deck.
 * 
 */
class RemoveListTableBinding implements uiSelectTableBinding
{
  protected
    $_selection = null;
  
  public function getConfig()
  {
    coreToolkit::loadHelpers(array('Date', 'CJK'));
    
    $this->_selection = uiSelectionState::getSelection(manageActions::REMOVE_FLASHCARDS);
    
    return <<< EOD
    {
      settings: {
        primaryKey: ['framenum']
      },
      columns: [
        {
          caption:   'Index',
          width:     5,
          cssClass:  'center',
          colData:  'framenum'
        },
        {
          caption:   'Kanji',
          width:     7,
          cssClass:  'kanji',
          colData:  'kanji',
          colDisplay:  '_kanji'
        },
        {
          caption:   'Keyword',
          width:     10,
          cssClass:  'keyword',
          colData:  'keyword'/*,
          colDisplay:  '_keyword'*/
        },
        
        {
          caption:   'Pass',
          width:     1,
          cssClass:  'bold center',
          colData:  'successcount'
        },
        {
          caption:   'Fail',
          width:     1,
          cssClass:  'center red',
          colData:  'failurecount',
          colDisplay: '_failurecount'
        },
        {
          caption:   'Box',
          width:     8,
          cssClass:  'bold center',
          colData:  'leitnerbox'
        },
        {
          caption:   'Last&nbsp;Review',
          width:     15,
          cssClass:  'center',
          colData:  'ts_lastreview',
          colDisplay:  '_lastreview'
        },
        {
          caption:   '<input type="checkbox" name="chkAll" value="all" class="chkAll" />',
          width:     1,
          cssClass:  'center',
          colDisplay:  '_checkbox'
        }
      ]
    }
EOD;
  }

  public function filterDisplayData(uiSelectTableRow $row)
  {
    $rowData =& $row->getRowData();

    $rowData['_failurecount'] =  $rowData['failurecount']==0 ? '' : $rowData['failurecount'];
    $rowData['_kanji'] = cjk_lang_ja($rowData['kanji']);
  //  $rowData['_keyword'] = link_to_keyword($rowData['keyword'], $rowData['framenum']);
    
    $tsLastReview = (int)$rowData['tsLastReview'];
    $rowData['_lastreview'] = $tsLastReview ? format_date($tsLastReview, rtkLocale::DATE_SHORT) : '-';

    $id = $rowData['framenum'];
    $rowData['_checkbox'] = $this->_selection->getInputTag('rf', $id) . $this->_selection->getCheckboxTag('rf', $id);
    if ($this->_selection->getState($id))
    {
      $row->addCssClass(array('selected'));
    }
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
