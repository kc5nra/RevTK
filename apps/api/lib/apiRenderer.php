<?php
/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file contains a function for every single REST endpoint.
 * The job of each function is to take a variable number of arguments
 * and construct an array() in the correct format to feed to the
 * json parser.
 *
 * Endpoints:
 *    boxes/*:
 *      boxesGet($boxes, $untestedCount)                GET boxes
 *      boxesIdGet($boxData)                            GET boxes/{boxId}
 *
 *    users/*:
 *      usersGet()                                      GET users
 *      usersIdGet($user)                                GET users/{userId}
 *
 *    cards/*:
 *      cardsIdStoriesGet()                              GET cards/{cardId}/stories
 *
 *    rest/*:
 *      restApiKeyGet($apiKey)                          GET rest/apiKey
 *
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 28 March, 2010
 * @package api
 * @subpackage helper
 **/

class apiRenderer {

  /**
   * GET rest/apiKey
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => STRING($apiKey)
   *  }
   *
   * @param $apiKey apiKey
   * @return response
   **/
  public static function restApiKeyGet($apiKey)
  {
    $apiResponse    = null;

    // add to the response
    $apiResponse['response'] = $apiKey;

    return $apiResponse;
  }

  /**
   * GET boxes
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => {
   *      [0..n] = {
   *        ['expiredCards']  => INTEGER
   *        ['freshCards']    => INTEGER
   *        ['totalCards']    => INTEGER
   *      }
   *      ['untestedCount']    => INTEGER
   *    }
   * @param $boxes              an array of boxes
   * @param $untestedCardsTotal number of untested cards
   * @return response
   **/
  public static function boxesGet($boxes, $untestedCount)
  {
    $apiResponse  = null;
    $newBoxHolder = null;

    $boxId = 0;
    foreach ($boxes as $box) {
      // temporary box, avoid value copy
      $box      = &$boxes[$boxId];

      // add what used to be the array index (boxId) as a child of the array
      // bump up each index by 1 (all sql statements re: boxes are 1 origin)
      $newBox['id']              = $boxId + 1;
      $newBox['expiredCards']    = $box['expired_cards'];
      $newBox['freshCards']      = $box['fresh_cards'];
      $newBox['totalCards']      = $box['total_cards'];

      // add new box to the new box holder
      $newBoxHolder[] = $newBox;

      $boxId++;
    }

    // add a pseudo-box alled 'untestedCount'
    $newBoxHolder['untestedCount'] = $untestedCount;

    // add to the response
    $apiResponse['response'] = $newBoxHolder;

    return $apiResponse;
  }

  /**
   * GET boxes/{boxId}
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => {
   *      [0..n] = {
   *        ['id']            => INTEGER
   *        ['keyword']        => STRING
   *        ['kanji']:        => STRING
   *        ['onyomi']        => STRING
   *        ['lessonNumber']  => INTEGER
   *        ['strokeCount']    => INTEGER
   *    }
   *  }
   *
   * @param $boxData an array of cards in the box
   * @return response
   **/
  public static function boxesIdGet($boxData)
  {
    $apiResponse    = null;
    $newCardHolder  = null;

    // loop through and create a new card
    foreach($boxData as &$card) {

      // copy relevant fields
      $newCard['id']            = $card['id'];
      $newCard['kanji']          = $card['kanji'];
      $newCard['onyomi']        = $card['onyomi'];
      $newCard['lessonNumber']  = $card['lessonnum'];
      $newCard['strokeCount']    = $card['strokecount'];

      // add the card to the card holder
      $newCardHolder[] = $newCard;
    }

    // add to the response
    $apiResponse['response'] = $newCardHolder;

    return $apiResponse;

  }

  /**
   * GET cards/{cardId}/stories
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => {
   *      [0..n] = {
   *        ['userId']        => INTEGER
   *        ['userName']      => STRING
   *        ['heisigNumber']: => INTEGER
   *        ['lastModified']  => DATE
   *        ['text']          => STRING
   *        ['stars']          => INTEGER
   *        ['kicks']          => INTEGER
   *    }
   *  }
   *
   * @param $stories an array of stories for a card
   * @return response
   **/
  public static function cardsIdStoriesGet($stories)
  {
    $apiResponse            = null;
    $newStoriesEntryHolder  = null;

    // loop through and create new news entries
    // in this case they are all the same
    foreach($stories as &$story) {
      // copy relevant fields and fix typing
      $newStoryEntry['userId']        = (int) $story['userid'];
      $newStoryEntry['userName']      =        $story['username'];
      $newStoryEntry['heisigNumber']  = (int) $story['framenum'];
      $newStoryEntry['lastModified']  =        $story['lastmodified'];
      $newStoryEntry['text']          =        $story['text'];
      $newStoryEntry['stars']          = (int) $story['stars'];
      $newStoryEntry['kicks']          = (int) $story['kicks'];
      // add the news entry to the new news entry holder
      $newStoriesEntryHolder[] = $newStoryEntry;
    }

    // add to the response
    $apiResponse['response'] = $newStoriesEntryHolder;

    return $apiResponse;

  }

  /**
   * GET users/{userId}
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => {
   *      ['id']              => INTEGER
   *      ['userName']        => STRING
   *      ['joinDate']:        => STRING
   *      ['lastLogin']        => STRING
   *      ['location']        => STRING
   *      ['timeZone']        => INTEGER
   *    }
   *  }
   *
   * @param $user an array containing a user's information
   * @return response
   **/
  public static function usersIdGet($user)
  {
    $apiResponse    = null;

    // copy relevant fields
    $newUser['id']          = $user['userid'];
    $newUser['userName']    = $user['username'];
    $newUser['joinDate']    = $user['joindate'];
    $newUser['lastLogin']    = $user['lastlogin'];
    $newUser['location']    = $user['location'];
    $newUser['timeZone']    = $user['timezone'];

    // add to the response
    $apiResponse['response'] = $newUser;

    return $apiResponse;
  }

  /**
   * GET news
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => {
   *      [0..n] = {
   *        ['id']            => INTEGER
   *        ['subject']        => STRING
   *        ['text']:          => STRING
   *        ['date']          => DATE
   *        ['brief']          => BOOLEAN
   *    }
   *  }
   *
   * @param $latestNewsEntries an array of the latest news entries
   * @return response
   **/
  public static function newsGet($latestNewsEntries)
  {
    $apiResponse        = null;
    $newNewsEntryHolder = null;

    // loop through and create new news entries
    // in this case they are all the same
    foreach($latestNewsEntries as &$newsEntry) {

      // copy relevant fields and fix typing
      $newNewsEntry['id']        = (int) $newsEntry['id'];
      $newNewsEntry['subject']  =        $newsEntry['subject'];
      $newNewsEntry['text']      =        $newsEntry['text'];
      $newNewsEntry['date']      =        $newsEntry['date'];
      $newNewsEntry['brief']    = (bool)$newsEntry['brief'];

      // add the news entry to the new news entry holder
      $newNewsEntryHolder[] = $newNewsEntry;
    }

    // add to the response
    $apiResponse['response'] = $newNewsEntryHolder;

    return $apiResponse;

  }

  /**
   * GET news/{newsId}
   *
   * Pseudo Schema:
   *
   *  {
   *    ['response'] => {
   *      ['id']            => INTEGER
   *      ['subject']        => STRING
   *      ['text']:          => STRING
   *      ['date']          => DATE
   *      ['brief']          => BOOLEAN
   *    }
   *  }
   *
   * @param $newsEntry a particular news post
   * @return response
   **/
  public static function newsIdGet($newsEntry)
  {
    $apiResponse        = null;
    $newNewsEntry        = null;


    // copy relevant fields and fix typing
    $newNewsEntry['id']        = (int) $newsEntry['id'];
    $newNewsEntry['subject']  =        $newsEntry['subject'];
    $newNewsEntry['text']      =        $newsEntry['text'];
    $newNewsEntry['date']      =        $newsEntry['date'];
    $newNewsEntry['brief']    =        false;

    // add to the response
    $apiResponse['response'] = $newNewsEntry;

    return $apiResponse;

  }

  /**
   * Rest Exception, this is not called by anyone except api Exception classes.
   *
   * Pseudo Schema:
   *
   *  {
   *    ['error'] = {
   *      ['message']      => STRING
   *      ['statusCode']  => INTEGER
   *    }
   *  }
   *
   * @param $apiKey APIKEY
   * @return response
   **/
  public static function restException($message, $statusCode)
  {
    $apiResponse          = null;
    $error['message']      = $message;
    $error['statusCode']  = $statusCode;

    // add to the error
    $apiResponse['error'] = $error;

    return $apiResponse;
  }

}
?>