<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Shared Stories component.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class SharedStoriesComponent extends coreComponent
{
  /**
   * Show publicly shared stories
   * 
   * PARAMS
   *   kanjiData      Kanji data for currently viewed kanji
   * 
   */
  public function execute($request)
  {
    $this->new_stories = StoriesPeer::getPublicStories($this->kanjiData->framenum, $this->kanjiData->keyword, true);
    $this->old_stories = StoriesPeer::getPublicStories($this->kanjiData->framenum, $this->kanjiData->keyword, false);

    return coreView::SUCCESS;
  }
}

