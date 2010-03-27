<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rkFlashcardDeck represents a collection of flashcards.
 * 
 * In the database flashcards are handled by ReviewsPeer. Currently there
 * is no support for multiple flashcard decks, but if there were this would
 * be a FlashcardDeckPeer or something, hence the static methods.
 * 
 * @package    RevTK
 * @subpackage FlashcardDeck
 * @author     Fabrice Denis
 */

class rtkFlashcardDeck
{
  protected static
    $userId     = null;

  /**
   * Checks for flashcard ids in the selection that already exist in the
   * user's deck and remove them. Returns a selection of ids that don't
   * exist in the user's deck.
   * 
   * @return 
   * @param object $selIds
   */
  static public function checkSelection($userId, array $cards)
  {
    // create array of flashcard ids that are in the user's deck
    $userCards = ReviewsPeer::getFlashcardIds($userId);

    // remove ids in the selection that are already in the user's deck
    // to avoid doing INSERTs on existing records 
    $dups = array_intersect($cards, $userCards);
    $selK = array();
    foreach ($cards as $id)
    {
      $selK[(int)$id] = 1;
    }
    foreach ($dups as $dupId)
    {
      unset($selK[(int)$dupId]);
    }
    
    return array_keys($selK);
  }

  /**
   * Add a set of new flashcard to the user's deck.
   * 
   * Duplicates and ids that laready exist in the user's deck must
   * be checked before (cf. checkSelection()).
   * 
   * @param object $userId
   * @param object $newCards
   * 
   * @return array  Array of successfully added flashcard ids
   */
  static public function addSelection($userId, array $cardSelection)
  {
    // create new flashcards
    $cards = ReviewsPeer::addFlashcards($userId, $cardSelection);
    if (count($cards))
    {
      ActiveMembersPeer::updateFlashcardCount($userId);
    }
    return $cards;
  }

  /**
   * Delete all given flashards, returns an array of ids of flashcards
   * that were succesfully deleted.
   * 
   * @param object $userId
   * @param object $selection
   * 
   * @return array  Array of successfully deleted flashcards (ids) or false
   */
  static public function deleteSelection($userId, array $cardSelection)
  {
    $cards = ReviewsPeer::deleteFlashcards($userId, $cardSelection);
    if (is_array($cards) && count($cards))
    {
      ActiveMembersPeer::updateFlashcardCount($userId);
    }
    return $cards;
  }
}
