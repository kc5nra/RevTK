<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtkFlashcardSelection creates selections of kanji flashcards
 * 
 * @package    RevTK
 * @subpackage FlashcardSelection
 * @author     Fabrice Denis
 */

class rtkFlashcardSelection
{
  protected
    $itemIds       = array(),
    $request       = null;
  
  /**
   * 
   * @param  object  $request  Object with setError() method
   * @return 
   */
  public function __construct($request)
  {
    $this->request = $request;
  }
  
  /**
   * Set array of flashcard ids, from a selection expressed as a string.
   * 
   * Accepts:
   *  Single cards      3
   *  Range of cards    4-25
   *  Kanji             <single utf8 char>
   *   
   * Delimiters:
   *  All flashcard ids (numerical or kanji) must be separated by commas,
   *  or spaces, or tabs. A range of cards can not have spaces around the dash.
   *  Kanji characters do not need to be separated between them but must be separated
   *  from the numerical indices eg:
   *  
   *   3, 42 15, 10-13 一年生
   * 
   * @param  string  $selString  Selection in string format
   * 
   * @return int   Number of cards in selection
   */
  public function setFromString($selString)
  {
    $this->itemIds = array();

    // split string on spaces, japanese space (0x3000) and comma
    $selparts = preg_split('/[,\s\x{3000}]+/u', $selString, -1, PREG_SPLIT_NO_EMPTY);
    if (!count($selparts))
    {
      return false;
    }
    
    foreach ($selparts as &$part)
    {
      // numerical range
      if (preg_match('/^([0-9]+)-([0-9]+)$/', $part, $matches))
      {
        $from = $matches[1];
        $to = $matches[2];
        if (!rtkBook::isValidRtkFrameNum($from) || !rtkBook::isValidRtkFrameNum($to))
        {
          $this->request->setError('if', sprintf('Invalid framenumber: "%s"', $part));
          return false;
        }
        elseif ($from > $to)
        {
          $this->request->setError('ir', sprintf('Invalid range: "%s"', $part));
          return false;
        }
        
        for ($i = $from; $i <= $to; $i++)
        {
          $this->itemIds[] = $i;
        }
        
      }
      // numerical id
      elseif (ctype_digit($part))
      {
        $framenum = intval($part);
        if (!rtkBook::isValidRtkFrameNum($framenum))
        {
          $this->request->setError('if', sprintf('Invalid framenumber: "%s"', $part));
          return false;
        }
        $this->itemIds[] = $framenum;
      }
      // utf8 character id
      elseif (CJK::hasKanji($part))
      {
        $cjkChars = CJK::getKanji($part);
        if (!count($cjkChars)) {
          continue;
        }

        foreach ($cjkChars as $cjk)
        {
          $framenum = rtkBook::getIndexForKanji($cjk);
          if ($framenum)
          {
            $this->itemIds[] = $framenum;
          }
          else
          {
            $this->request->setError('if', sprintf('Cannot add non-Heisig character: "%s"', $part));
            return false;
          }
        }
      }
      else
      {
        $this->request->setError('ip', sprintf('Invalid part: "%s"', $part));
        return false;
      }
    }

    // remove duplicates
    $this->itemIds = array_unique($this->itemIds);

    return $this->getNumCards();
  }

  /**
   * Add cards in Heisig order.
   * 
   * Selection should be a frame number to add up to,
   * or a number of cards to add "+10", filling in all missing cards in the RTK range.
   * 
   * @param string $selection  "56" (add up to 56), or "+20" (add 20 cards)
   * 
   * @return int   Number of cards in selection (also 0), or false if error
   */
  public function addHeisigRange($userId, $selection)
  {
    $this->itemIds = array();

    // get user's existing flashcard ids in RTK range
    $userCards = ReviewsPeer::getFlashcardIds($userId, 'rtk1+3');
    
    // map in an array, 1 means card exists, 0 it doesn't
    $inDeck = array();
    foreach ($userCards as $id)
    {
      $inDeck[(int)$id] = true;
    }
    
    // add a number of cards, or up to frame number, fill in the missing cards
    if (preg_match('/^\+([0-9]+)$/', $selection, $matches))
    {
      $range = $matches[1];

      if ($range < 1)
      {
        $this->request->setError('x', 'Invalid range of cards');
        return false;
      }
      
      for ($i = 1, $n = 0; $n < $range && $i <= rtkBook::MAXKANJI_VOL3; $i++)
      {
        if (!isset($inDeck[$i]))
        {
          $this->itemIds[] = $i;
          $n++;
        }
      }
    }
    else
    {
      $addTo = intval($selection);
      
      if (!rtkBook::isValidRtkFrameNum($addTo))
      {
        $this->request->setError('x', sprintf('Invalid index number: "%s"', $selection));
        return false;
      }

      for ($i = 1; $i <= $addTo; $i++)
      {
        if (!isset($inDeck[$i]))
        {
          $this->itemIds[] = $i;
        }
      }
    }
    
    return $this->getNumCards();
  }


  /**
   * 
   * @return int  Number of items in selection
   */
  public function getNumCards()
  {
    return count($this->itemIds);
  }

  /**
   * 
   * @return array  Array of item ids (the selection)
   */
  public function getCards()
  {
    return $this->itemIds;
  }

  /**
   * Serialize methods to save user selection between requests.
   * 
   * @return 
   */
  public function __sleep()
  {
    return array('itemIds');
  }

  public function __wakeup()
  {
  }
}
