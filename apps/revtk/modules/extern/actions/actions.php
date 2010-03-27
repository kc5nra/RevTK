<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Greasemonkey script dependencies
 * 
 * @package    RevTK
 * @subpackage extern
 * @author     Fabrice Denis
 */

class externActions extends coreActions
{
  public function executeIndex()
  {
  }

  /**
   * Fontpicker is a page using a flash file (SWF) to get a listing of all fonts
   * on a user's computer, and allow one of the Greasemonkey Scripts to let the
   * user pick a font and change the fonts used by the flashcards and on other
   * places.
   * 
   * @see   Woelpad's scripts 
   *        http://forum.koohii.com/viewtopic.php?id=518
   */
  public function executeFontpicker()
  {
  }
}
