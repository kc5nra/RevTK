<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Kanji Flashcard Review Summary
 * 
 * Display a list of the flashcards from the last review session,
 * and a graphi indicating the ratio of remembered vs forgotten cards.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

class summaryAction extends coreAction
{
  public function execute($request)
  {
    if ($request->getMethod()===coreRequest::POST)
    {
      // validate post parameters and save user's review session info
      $validator = new coreValidator($this->getContext()->getActionName());
      $this->forward404Unless($validator->validate($request->getParameterHolder()->getAll()));
      
      $this->saveReviewSessionInfo($request->getParameterHolder());

      // update review stats for the active members list
      ActiveMembersPeer::updateFlashcardInfo($this->getUser()->getUserId());
    }
    else
    {
      // grab the user's most recent review session info from db
      $params = ActiveMembersPeer::getReviewSummaryInfo($this->getUser()->getUserId());
      $this->forward404Unless($params);
      $request->getParameterHolder()->add($params);
    }

    // template vars
    $this->ts_start = $request->getParameter('ts_start', 0);
    $this->numRemembered = (int) $request->getParameter('fc_pass', 0);
    $this->numForgotten = (int) $request->getParameter('fc_fail', 0);
    $this->numTotal = $this->numRemembered + $this->numForgotten;
    
    if ($this->numRemembered == $this->numTotal) {
      $this->title = 'Hurrah! All remembered!';
    }
    elseif ($this->numForgotten == $this->numTotal && $this->numTotal > 1) {
      $this->title = 'Eek! All forgotten!';
    }
    else {
      $this->title = 'Remembered '.$this->numRemembered.' of '.$this->numTotal.' kanji.';
    }
  }
  
  /**
   * Save information from the last flashcard review session in the database.
   * Allows to see the last review session results on subsequent GET request,
   * and until the user starts another review session.
   * 
   * The data could also be used in the active member statistics, ..
   * 
   * @param object $params
   */
  protected function saveReviewSessionInfo(sfParameterHolder $params)
  {
      $data = array(
        'ts_start' => $params->get('ts_start'),
        'fc_pass'  => $params->get('fc_pass'),
        'fc_fail'  => $params->get('fc_fail')
      );
      ActiveMembersPeer::saveReviewSummaryInfo($this->getUser()->getUserId(), $data);
  }
}
