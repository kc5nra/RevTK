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
 * @package    RevTK
 * @subpackage main
 * @author     Fabrice Denis
 */

class reviewActions extends coreActions
{
  /**
   * Review graph page.
   * 
   * @return 
   */
  public function executeIndex($request)
  {
    // set local pref default value
    $this->filter = $this->getUser()->getLocalPrefs()->sync('review.graph.filter', null, '');
    if ($this->filter == '')
    {
      $this->filter = 'all';
    }
  
    $this->total_flashcards = ReviewsPeer::getFlashcardCount($this->getUser()->getUserId());
  }

  /**
   * Review graph filter actions.
   * 
   * @return 
   */
  public function executeAjaxLeitnerGraph($request)
  {
    $filter = $request->getParameter('filter', '');
    if (!preg_match('/^(all|rtk1|rtk3)$/', $filter))
    {
      throw new rtkAjaxException('Invalid parameters.');
    }

    if ($filter=='all') {
      $filter = '';
    }
    
    // remember last filter selected
    $this->getUser()->getLocalPrefs()->sync('review.graph.filter', $filter, '');
    
    return $this->renderComponent('review', 'LeitnerChart');
  }

  /**
   * Review within the web site layout.
   * 
   * @return 
   */
  public function executeReview($request)
  {
    return $this->reviewAction($request);
  }

  /**
   * Review in fullscreen mode (use a layout without header, navigation, etc).
   * 
   */
  public function executeFullscreen($request)
  {
    $this->setLayout('fullscreenLayout');
    return $this->reviewAction($request);
  }

  /**
   * Kanji Flashcard review page with uiFlashcardReview
   * 
   * GET request = review page
   * 
   *    type = 'expired'|'untested'|'relearned'|'fresh'
   *   box  = 'all'|[1-5]
   *   filt = ''|'rtk1'|'rtk3'
   *   
   * POST request = ajax request during review
   * 
   * @param object $request
   */
  protected function reviewAction($request)
  {
    $reviewBox  = $request->getParameter('box', 'all');
    $reviewType = $request->getParameter('type', 'expired');
    $reviewFilt = $request->getParameter('filt', '');
    $reviewMerge= $request->getParameter('merge') ? true : false;

    // validate
    $this->forward404Unless( preg_match('/^(all|[1-9]+)$/', $reviewBox) );
    $this->forward404Unless( preg_match('/^(expired|untested|relearned|fresh)$/', $reviewType) );
    $this->forward404Unless( $reviewFilt=='' || preg_match('/(rtk1|rtk3)/', $reviewFilt) );

    // pick title
    $this->setReviewTitle($reviewType, $reviewFilt);

    // 
    $sAjaxUrl = $this->getController()->genUrl('@review');
    
    $options = array(
      'partial_name'     => 'review/ReviewKanji',
      'ajax_url'         => $sAjaxUrl,
      'ts_start'         => ReviewsPeer::getLocalizedTimestamp(),
      'fn_get_flashcard' => array('KanjisPeer', 'getFlashcardData'),
      'fn_put_flashcard' => array('ReviewsPeer', 'putFlashcardData')
    );

    if ($request->getMethod()!==coreRequest::POST)
    {
      $options['items'] = ReviewsPeer::getFlashcards($reviewBox, $reviewType, $reviewFilt, $reviewMerge);
      
      $this->uiFR = new uiFlashcardReview($options);
    }
    else
    {
      /*
      if (rand(1,10) < 3)
      {
        sleep(6);
      }*/
      
      // handle Ajax request (or throws exception)
      
      $oJson = coreJson::decode($request->getParameter('json', '{}'));
      if (!empty($oJson))
      {
        $flashcardReview = new uiFlashcardReview($options);
        return $this->renderText( $flashcardReview->handleJsonRequest($oJson) );
      }
      
      throw new rtkAjaxException('Empty JSON Request.');
      
    }
    return coreView::SUCCESS;
  }

  /**
   * Sets template variable for the Review session title
   * 
   */
  protected function setReviewTitle($reviewType, $reviewFilt)
  {
    $titles = array(
      'expired'   => 'Due flashcards',
      'untested'  => 'New flashcards',
      'relearned' => 'Relearned flashcards',
      'fresh'     => 'Undue flashcards'
    );
    $this->title = isset($titles[$reviewType]) ? $titles[$reviewType] : 'Flashcards';
  }

  /**
   * Summary Table ajax
   * 
   * @return 
   */
  public function executeSummaryTable($request)
  {
    $ts_start = $request->getParameter('ts_start', 0);
    $this->forward404Unless(BaseValidators::validateInteger($ts_start));
    return $this->renderComponent('review', 'summaryTable', array('ts_start' => $ts_start));
  }
}
