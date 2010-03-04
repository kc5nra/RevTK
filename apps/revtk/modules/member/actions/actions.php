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
 * @subpackage member
 * @author     Fabrice Denis
 */

class memberActions extends coreActions
{
	public function executeIndex()
	{
	}

	public function executeProgress()
	{
		// determine active lesson if the user has added cards in order,
		//  otherwise set to FALSE
		$cur_frame = ReviewsPeer::getHeisigProgressCount($this->getUser()->getUserId());
		$this->currentLesson = $cur_frame ? rtkBook::getLessonForFramenum($cur_frame + 1) : false;
		
		// find the success/fail flashcard count per lesson
    $progress = ReviewsPeer::getProgressStatus($this->getUser()->getUserId());

    // rtk lesson data
    $rtkLessons = rtkBook::getLessons();

    // how many lessons have been started
    $this->activeLessons = 0;
    
    $lessons = array();
    for ($i = 1; $i <= 56; $i++)
    {
      $lessons[$i] = array(
        'label'      => 'Lesson '.$i,
        'passValue'  => 0,
        'failValue'  => 0,
        'testedCards'=> 0,
        'totalCards' => 0,
        'maxValue'   => $rtkLessons[$i]
      );
    }
    
    foreach ($progress as $p)
    {
    	if ($p->lessonId <= 0) {
    		throw new coreException('Bad lesson id');
    	}
    	// fixme: only RtK1 for now
    	if ($p->lessonId > 56) {
    		break;
    	}
    	$lessons[$p->lessonId] =  array(
        'label'      => 'Lesson '.$p->lessonId,
        'passValue'  => $p->pass,
    	  'failValue'  => $p->fail,
    	  'testedCards'=> $p->pass + $p->fail,
    	  'totalCards' => $p->total,
        'maxValue'   => $rtkLessons[$p->lessonId]
      );
      $this->activeLessons++;
    }

    $this->lessons = $lessons;    
	}

  /**
   * Export a Trinity(alpha) user's vocab/sentence flashcards.
   * 
   * Request parameters:
   * 
   *   mode    'vocab' to export the user's vocab flashcards
   *           'sentences' to export the user's sentence flashcards
   * 
   * @date  2009-10-30
   */
  public function executeExportTrinity($request)
  {
    $exportMode = $this->getRequestParameter('mode'); 
    if (!in_array($exportMode, array('vocab', 'sentences'))) {
    	throw new coreException("error");
    }  	

    $throttler = new RequestThrottler($this->getUser(), 'trinity.export');
    if (!$throttler->isValid()) {
      return $this->renderPartial('misc/requestThrottleError');
    }

    $db = $this->getContext()->getDatabase();
    $csv = new ExportCSV($db);
    
    // We build the select here, Trinity is no longer supported, no need for extra peer classes.
    if ($exportMode === 'vocab')
    { 
	    $select = $db->select(array(
	      'compound', 'reading', 'glossary',
	      'itemid', 'dateadded', 'lastreview', 'leitnerbox', 'failurecount', 'successcount'
	      ))
	      ->from(array('r' => 'vocabreviews'))
	      ->join(array('d' => 'jdict'), 'r.itemid = d.dictid')
	      ->where('userid = ?', $this->getUser()->getUserId());
	
	    $csvText = $csv->export($select, array(
	      'compound', 'reading', 'definition',
	      'dictid', 'dateadded', 'lastreview', 'leitnerbox', 'failcount', 'passcount'
	    ), array('col_escape' => array(1, 1, 1, 0,0,0,0,0,0), 'column_heads' => false));
	    
	    $downloadName = 'trinity_vocab.csv'; 
    }
    else if ($exportMode === 'sentences')
    {
      $select = $db->select(array(
        'question', 'answer', 'sn.spakid', 'sp.title', 'sn.lastedited',
        'sn.dateadded', 'lastreview', 'leitnerbox', 'failurecount', 'successcount'
        ))
        ->from(array('sr' => 'sentencereviews'))
        ->join(array('sn' => 'sentences'), 'sr.itemid = sn.sentenceid AND sr.userid = sn.userid')
        ->join(array('sp' => 'sentencepacks'), 'sn.spakid = sp.spakid AND sn.userid = sp.userid')
        ->where('sr.userid = ?', $this->getUser()->getUserId());
  
      $csvText = $csv->export($select, array(
        'question', 'answer', 'sp_id', 'sp_title', 'lastedited',
        'dateadded', 'lastreview', 'leitnerbox', 'failcount', 'passcount'
      ), array('col_escape' => array(1,1,0,1,0, 0,0,0,0,0), 'column_heads' => false));
      
      $downloadName = 'trinity_sentences.csv'; 
    }

    $throttler->setTimeout();
    
    $this->getResponse()->setFileAttachmentHeaders($downloadName);

    $this->setLayout(false);

    return $this->renderText($csvText);
  }
	

}
