<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * View/Edit Story component.
 * 
 * This component main action is designed to work as part of the Study page,
 * but also as a ajax component on the Flashcard Review pages.
 * 
 * JSON message:
 * 
 *   action       'save' or 'delete'
 *   postdata     All postdata variables from submitted FORM
 * 
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class EditStoryComponent extends coreComponent
{
  /**
   * Return EditStory component based on GET or POST request.
   * 
   * PARAMS
   *   framenum       Valid kanji id (frame number)
   *   kanjiData      Kanji data for kanji id
   *   reviewMode     True if called from the Review page
   *   
   * POST  requests to update the story for current user.
   * 
   *   character      Current kanji (utf8)
   *   chkPublic      Public story
   *   txtStory       Story
   * 
   * 
   * @return 
   * @param object $request
   */
  public function execute($request)
  {
    if ($request->getMethod() !== coreRequest::POST)
    {
      // get user's story
      $story = StoriesPeer::getStory($this->getUser()->getUserId(), $this->framenum);
      if ($story)
      {
        $request->getParameterHolder()->add(array(
          'txtStory'  => $story->text,
          'chkPublic' => $story->public
        ));
      }
    }
    else
    {
      $validator = new coreValidator($this->getActionName());

      if ($validator->validate($request->getParameterHolder()->getAll()))
      {
        if ($request->hasParameter('doUpdate'))
        {
          $txtStory = trim($request->getParameter('txtStory', ''));
          $txtStory = strip_tags($txtStory);
          
          // delete empty story
          if (empty($txtStory))
          {
            StoriesPeer::deleteStory($this->getUser()->getUserId(), $this->framenum);
          }
          else
          {
            StoriesPeer::updateStory($this->getUser()->getUserId(), $this->framenum, array
            (
              'text'     => $txtStory,
              'public'   => $request->hasParameter('chkPublic') ? 1 : 0
            ));
          }
          
          $request->setParameter('txtStory', $txtStory);
        }
      }
    }

    // set state
    $request->setParameter('framenum', $this->framenum);

    if (!$request->hasParameter('reviewMode'))
    {
      $this->isRestudyKanji = ReviewsPeer::isRestudyKanji($this->getUser()->getUserId(), $this->framenum);
      $this->isRelearnedKanji = LearnedKanjiPeer::hasKanji($this->getUser()->getUserId(), $this->framenum);
    }

    $this->formatted_story = StoriesPeer::getFormattedStory($request->getParameter('txtStory', ''), $this->kanjiData->keyword, true);

    return coreView::SUCCESS;
  }
}

