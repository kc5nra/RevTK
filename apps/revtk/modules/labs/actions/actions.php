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
 * @subpackage ___
 * @author     Fabrice Denis
 */

class labsActions extends coreActions
{
  public function executeIndex($request)
  {
    // handle ajax requests (POST)
    if ($request->getMethod()!==coreRequest::POST)
    {
      $request->setParameter('kanji', 'むかし、むかし、ご存知のとおり、うさぎとかめは、山の上まで競争しました。誰もが、うさぎの方がかめよりも早くそこに着くと思いました。しかし迂闊にも、うさぎは途中で寝てしまいました。目が覚めた時は、もうあとのまつりでした。かめはすでに山のてっ辺に立っていました。');
    }
    else
    {
      // filter out all non kanji
      $s = $request->getParameter('kanji');
      $cjk = CJK::getKanji($s);
      if (!count($cjk)) {
        $request->setError('foo','error');
        return;
      }
      
      if (!count($cjk)) {
        continue;
      }

      foreach ($cjk as $cjk)
      {
      }
      
    }
  }
  
  public function executeReview($request)
  {
    // fullscreen mode for later
    //$this->setLayout('fullscreenLayout');
    return $this->reviewAction($request);
  }

  protected function reviewAction($request)
  {
    $db = $this->getContext()->getDatabase();

    $options = array(
      'fn_get_flashcard' => array('rtkLabs', 'getFlashcardData')
    );

    if ($request->getMethod()!==coreRequest::POST)
    {
      $options['items'] = rtkLabs::iVocabShuffleBegin();
      
      $this->uiFR = new uiFlashcardReview($options);
    }
    else
    {
      // handle Ajax requests
      $oJson = coreJson::decode($request->getParameter('json', '{}'));
      if (!empty($oJson))
      {
        $flashcardReview = new uiFlashcardReview($options);
        return $this->renderText( $flashcardReview->handleJsonRequest($oJson) );
      }
      throw new rtkAjaxException('Empty JSON Request.');
    }
  }
}
