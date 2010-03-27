<?php
/**
 * Rtk Kanji Flashcards Peer.
 * 
 * This table stores the review status of all kanji flashcards for all users.
 * 
 * Methods:
 *  getFlashcardIds()
 *  addFlashcards()
 *  deleteFlashcards()
 *  getFlashcardCount()
 *  getCountExpired()
 *  getCountFailed()
 *  getCountUntested()
 *  getCountRtK3()
 *  getLeitnerBoxCounts()
 *  getReviewedFlashcardCount()
 *  getHeisigProgressCount()
 *  getTotalReviews()
 *  getMostRecentReviewTimeStamp()
 *  getDetailedFlashcardList()
 *  getSelectForExport()
 *  getKnownKanji()
 *  getFlashcards()
 *  
 * Helpers:
 * 
 *  filterByRtk()      Applies framenum filter to the select object
 *  filterByUserId()   Applies userid filter to the select object
 *  
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class ReviewsPeer extends coreDatabaseTable
{
  protected
    $tableName = 'reviews',
    $columns = array
    (
      'userid',
      'framenum',
      'lastreview',
      'expiredate',
      'totalreviews',
      'leitnerbox',
      'failurecount',
      'successcount'
    );

  /**
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

  /**
   * Return array of flashcard ids for user, optional RTK filter.
   * 
   * @param  int  $userId   User id
   * 
   * @return array  Array of flashcard ids, empty array if no cards
   */
  public static function getFlashcardIds($userId, $filter = null)
  {
    $select = self::getInstance()->select('framenum');
    $select = self::filterByUserId($select, $userId);
    if ($filter !== null)
    {
      $select = self::filterByRtk($select, $filter);
    }
    return self::$db->fetchCol($select);
  }
  
  /**
   * Add a set of new kanji flashcards at once,
   * Ids can not already exist in the table, and must be checked before!
   * 
   * @param int   $userId
   * @param array $cardIds  Array of flashcard ids (kanji framenum or unicode value)
   * 
   * @return array  Returns an array of succesfully created flashcards' ids
   */
  public static function addFlashcards($userId, $cardIds)
  {
    $tableName = self::getInstance()->getName();
    
    // lock the table (to speedup index) (minimal speed gain..)
    self::$db->query('LOCK TABLE '.$tableName.' WRITE');

    // prepare statement and execute for all cards
    $stmt = new coreDatabaseStatementMySQL(self::$db,
      sprintf('INSERT %s (userid,framenum,leitnerbox) VALUES (%d,?,1)', $tableName, $userId));

    try
    {
      $done = array();
      foreach ($cardIds as $id)
      {
        if (!$stmt->execute(array($id)))
        {
          break;
        }
        $done[] = $id;
      }
    }
    catch (coreException $e)
    {
    }

    // unlock table
    self::$db->query('UNLOCK TABLES');

    // return succesfully added ids
    return $done;
  }

  /**
   * Delete a set of flashcards.
   * 
   * @param int   $userId
   * @param array $cardIds  Array of flashcard ids (kanji framenum or unicode value)
   * 
   * @return array  Returns an array of succesfully deleted flashcard ids or false
   */
  public static function deleteFlashcards($userId, $cards)
  {
    $tableName = self::getInstance()->getName();
    
    // lock the table (to speedup index) (minimal speed gain..)
    self::$db->query('LOCK TABLE '.$tableName.' WRITE');

    // prepare statement and execute for all cards
    $stmt = new coreDatabaseStatementMySQL(self::$db,
      sprintf('DELETE FROM %s WHERE userid = %d AND framenum = ?', $tableName, $userId));

    try
    {
      $done = array();
      foreach ($cards as $id)
      {
        if (!$stmt->execute(array($id)))
        {
          break;
        }
        if ($stmt->rowCount() > 0) {
          $done[] = $id;
        }
      }
    }
    catch (coreException $e)
    {
      $done = false;
    }

    // unlock table
    self::$db->query('UNLOCK TABLES');

    // return succesfully added ids
    return $done;
  }

  /**
   * Returns count of kanji flashcards with given select object (where etc).
   * 
   * @param  int    $userId  User id.
   * @param  coreDatabaseSelect  $select   Select object with where clause(s) applied.
   * 
   * @return 
   */
  protected static function _getFlashcardCount($userId, $select = null)
  {
    if (is_null($select))
    {
      $select = self::getInstance()->select();
    }

    $select->from(self::getInstance()->getName());
    $select->columns(array('count' => 'COUNT(*)'));
    $select = self::filterByUserId($select, $userId);
//DBG::printr($select->__toString());
    $select->query();
    $result = self::$db->fetchObject();
    return (int) $result->count;
  }

  /**
   * Return count of all flashcards for user.
   * 
   * @return 
   */
  public static function getFlashcardCount($userId)
  {
    return self::_getFlashcardCount($userId);
  }

  /**
   * Return count of expired flashcards (except failed cards) for user.
   * 
   * @param
   * 
   * @return
   */
  public static function getCountExpired($userId)
  {
    $user = coreContext::getInstance()->getUser();
    $sqlLocalTime = new coreDbExpr($user->sqlLocalTime());
    $select = self::getInstance()->select()->where('totalreviews>0 AND leitnerbox>1  AND expiredate <= ?', $sqlLocalTime);
    return self::_getFlashcardCount($userId, $select);
  }

  /**
   * Return count of failed(restudy) flashcards for user.
   * 
   * @param
   * 
   * @return
   */
  public static function getCountFailed($userId, $timezone = 0)
  {
    $user = coreContext::getInstance()->getUser();
    $select = self::getInstance()->select()->where('? >= expiredate AND totalreviews>0 AND leitnerbox=1', array(new coreDbExpr($user->sqlLocalTime($timezone))));
    return self::_getFlashcardCount($userId, $select);
  }

  /**
   * Return count of untested flashcards for user.
   * 
   * @param  string  $filter  See filterByRtk()
   * 
   * @return
   */
  public static function getCountUntested($userId, $filter = '')
  {
    $user = coreContext::getInstance()->getUser();
    $select = self::getInstance()->select()->where('totalreviews <= 0');
    $select = self::filterByRtk($select, $filter);
    return self::_getFlashcardCount($userId, $select);
  }

  /**
   * Return count of flashcards for RtK Volume 3 kanji.
   * 
   * @return 
   */
  public static function getCountRtK3($userId)
  {
    $user = coreContext::getInstance()->getUser();
    $select = self::getInstance()->select();
    $select = self::filterByRtk($select, 'rtk3');
    return self::_getFlashcardCount($userId, $select);
  }


  /**
   * Return flashcard counts for each Leitner card box.
   * 
   * Return value format:
   *   array(
   *     array(
   *       'expired_cards' => 20,
   *       'fresh_cards'   => 10,
   *       'total_cards'   => 30
   *     ),
   *     ...
   *   )
   * 
   * @param  string   $filter  Filter by RtK Volume 1, 3, or all (see filterByRtk())
   * 
   * @return array
   */
  public static function getLeitnerBoxCounts($filter = '')
  {
    $user = coreContext::getInstance()->getUser();

    $select = self::getInstance()->select(array(
        'box'   => 'leitnerbox',
        'v1'    => sprintf('(%s >= expiredate)', $user->sqlLocalTime()),
        'count' => 'COUNT(*)'
      ))
      ->where('totalreviews > 0')
      ->group(array('leitnerbox', 'v1 ASC'));
    
    $select = self::filterByUserId($select, $user->getUserId());
    $select = self::filterByRtk($select, $filter);
    $select->query();

    $boxes = array();

    for ($i = 0; $i < LeitnerSRS::MAXSTACKS; $i++)
    {
      $boxes[$i] = array('expired_cards' => 0, 'fresh_cards' => 0, 'total_cards' => 0);
    }

    while ($row = self::$db->fetchObject())
    {
      $i = intval($row->box - 1);

      if (!isset($boxes[$i]))
      {
        throw new coreException('getCardBoxCounts() unexpected box number');
      }

      if ($row->v1)
      {
        $boxes[$i]['expired_cards'] += $row->count;
      }
      else
      {
        $boxes[$i]['fresh_cards'] += $row->count;
      }
    }

    for ($i = 0; $i < LeitnerSRS::MAXSTACKS; $i++)
    {
      $boxes[$i]['total_cards'] = $boxes[$i]['expired_cards'] + $boxes[$i]['fresh_cards'];
    }
    
    return $boxes;
  }

  /**
   * Returns number of kanji flashcards with at least one review.
   * Used by Profile page.
   * 
   * @return int
   */
  public static function getReviewedFlashcardCount($userId)
  {
    $select = self::getInstance()->select()->where('totalreviews > 0');
    return self::_getFlashcardCount($userId, $select);
  }

  /**
   * Returns the number of kanji flashcards in Heisig order.
   * If there is any gap in the RtK frame number range, it returns false.
   *
   * @return mixed  Heisig progress count or false
   */
  public static function getHeisigProgressCount($userId)
  {
    // get the flashcard count in the RtK range
    $select = self::getInstance()->select()->where('framenum <= ?', rtkBook::MAXKANJI_VOL3);
    $flashcardCount = self::_getFlashcardCount($userId, $select);

    // compare that to the max frame number in that range
    $select = self::getInstance()->select(array('max'=>'MAX(framenum)'));
    self::filterByUserId($select, $userId)->query();
    $result = self::$db->fetchObject();
    $maxRtkFramenum = $result->max;

    if ($flashcardCount != $maxRtkFramenum)
    {
      return false;
    }
    
    return $maxRtkFramenum;
  }
  
  /**
   * Returns an associative array containing progress status for each
   * lesson, for the given user.
   * 
   * TODO: généraliser pour les lessons JLPT etc?
   * 
   * array(
   *   array(
   *     lessonId
   *     total
   *     pass
   *     fail
   *     
   * @return  array  Array of objects
   */
  public static function getProgressStatus($userId)
  {
    $select = self::getInstance()->select(array(
        'lessonId'  => 'lessonnum',
        'total'     => 'COUNT(*)',
        'pass'      => 'SUM(leitnerbox > 1)',
        'fail'      => 'SUM(leitnerbox = 1 AND totalreviews > 0)'
      ))
      ->joinLeftUsing(KanjisPeer::getInstance()->getName(), 'framenum')
      ->group('lessonnum ASC');

    $select = self::filterByUserId($select, $userId);
    //$select = self::filterByRtk($select, $filter);
    $select->query();
    
    $lessons = array();
    while ($row = self::$db->fetchObject())
    {
      $lessons[] = $row;
    }
    
    return $lessons;
  }

  /**
   * Return total reviews accross all kanji,
   * for the Profile page.
   * 
   * @return int
   */
  public static function getTotalReviews($userId)
  {
    $select = self::$db->select(array('count' => 'SUM(totalreviews)'))->from('reviews');
    self::filterByUserId($select, $userId)->query();
    $row = self::$db->fetchObject();
    return (int)$row->count;
  }

  /**
   * Return the most recent flash card review timestamp
   * (of a single flash card, not a review session).
   * 
   * @param  int    $userId   User id.
   * @return mixed  Lastest review timestamp, or FALSE
   */
  public static function getMostRecentReviewTimeStamp($userId)
  {
    $select = self::getInstance()->select('MAX(lastreview)');
    $select = self::filterByUserId($select, $userId);
    $ts_lastreview = self::$db->fetchOne($select);
    return !is_null($ts_lastreview) ? $ts_lastreview : false;
  }

  /**
   * Returns Select for detailed flashcard lists.
   * 
   * Used on:
   * - Detailed Flashcard List
   * - Manage Flashcards > Select flashcards to remove
   * 
   * @param
   * @return
   */
  public static function getDetailedFlashcardList($userId)
  {
    $select = self::getInstance()->select(array(
      'userid', self::getInstance()->getName().'.framenum', 'keyword', 'failurecount', 'successcount', 'leitnerbox',
      'ts_lastreview' => 'UNIX_TIMESTAMP(lastreview)', 'kanji', 'onyomi', 'strokecount',
      'tsLastReview' => 'UNIX_TIMESTAMP(lastreview)'
      ))
      ->joinLeftUsing(KanjisPeer::getInstance()->getName(), 'framenum');
    $select = self::filterByUserId($select, $userId);
    return $select;
  }
  
  /**
   * Returns select for flashcard export feature.
   * 
   * @param int  $userId
   */
  public static function getSelectForExport($userId)
  {
    $select = self::getInstance()->select(array(
      self::getInstance()->getName().'.framenum', 'kanji', 'keyword', 'lastreview', 'expiredate', 'leitnerbox', 'failurecount', 'successcount'))
      ->joinLeftUsing(KanjisPeer::getInstance()->getName(), 'framenum');
    $select = self::filterByUserId($select, $userId);
    return $select;
  }

  /**
   * Retrieve kanji info (given columns) for each known kanji for given user.
   * 
   * @param array $kanji_cols  One or more column names eg. 'keyword'
   * @return
   */
  public static function getKnownKanji($userId, $kanji_cols = array())
  {
    array_unshift($kanji_cols, KanjisPeer::getInstance()->getName().'.framenum');
    $select = self::$db->select($kanji_cols)->from(KanjisPeer::getInstance()->getName())
      ->joinLeftUsing(self::getInstance()->getName(), 'framenum');
    $select = self::filterByUserId($select, $userId);
    return self::$db->fetchAll($select);
  }

  /**
   * Get a selection of flashcards for review, as an array of flashcard ids.
   * 
   * Orders flashcards by expiredate (longest expired first), and then
   * randomize cards that expire on the same date.
   * 
   * Only works for current user (sqlLocalTime).
   * 
   * @param  mixed    $box    'all' or a Leitner box number starting from 1
   * @param  string   $type   'expired', 'untested', 'relearned', anything else means 'fresh' (non-expired)
   * @param  string   $filt   'rtk1', 'rkt3', '' for all kanji
   * @param  boolean  $merge  True to merge flashcards from given box with higher boxes
   * 
   * @return array    Flashcard ids (framenum).
   */
  public static function getFlashcards($box, $type, $filt, $merge)
  {
    $user = coreContext::getInstance()->getUser();
    $userId = $user->getUserId();
    $sqlLocalTime = new coreDbExpr($user->sqlLocalTime());

    if ($type === 'relearned')
    {
      // select cards from relearned kanji selection
      $select = LearnedKanjiPeer::getInstance()->select('framenum');
      $select->where('userid = ?', $userId);
    }
    else
    {
      $select = self::getInstance()->select('framenum');
      $select = self::filterByUserId($select, $userId);
      $select = self::filterByRtk($select, $filt);
    }

    switch ($type)
    {
      case 'untested':
        $select->where('totalreviews = 0');
        $select->order('expiredate, RAND()');
        break;
        
      case 'relearned':
        $select->order('RAND()');
        break;

      default:
        // expired or non-expired (orange or green stacks)
        if ($type == 'expired') {
          $select->where('totalreviews > 0 AND expiredate <= ?', $sqlLocalTime);
        }
        else {
          $select->where('totalreviews > 0 AND expiredate > ?', $sqlLocalTime);
        }
        
        if ($box == 'all')
        {
          $select->where('leitnerbox > 1');
        }
        elseif ($merge)
        {
          $select->where('leitnerbox >= ?', $box);
        }
        else
        {
          $select->where('leitnerbox = ?', $box);
        }
  
        $select->order('expiredate, RAND()');
        break;
    }

    //DBG::out($select);exit;

    return self::$db->fetchCol($select);
  }

  /**
   * Extract just the 'id' column from a select's resultset, and returns the ids as an array.
   * 
   * @param
   * @return
  public static function getArrayIds()
  {
    $rows = self::$db->fetchAll($select);
    $ids = array();
    foreach ($rows as $row)
    {
      $ids[] = $row['id'];
    }
    return $ids;
  }
   */

  
  /**
   * Return test array for flashcard review
   * 
   * @return 
   */
  public static function testFlashcards($userId)
  {
    $items = array();
    $select  = self::getInstance()->select(array('id'=>'framenum'))
        ->where('framenum > 0 AND framenum<=100'); //->order('RAND()')
    $select = self::filterByUserId($select, $userId);        

    $rows = self::$db->fetchAll($select);
    $ids = array();
    foreach ($rows as $row)
    {
      $ids[] = $row['id'];
    }
    return $ids;
  }

  /**
   * Update the flashcard review status (uiFlashcardReview callback)
   * 
   * Note: uiFlashcardReview callback, must sanitize data!
   * 
   * Flashcard answer data is set by the front end code (review.js):
   * 
   *    id     Flashcard id = framenum
   *    r      Answer (1=No  2=Yes  3=Easy)`
   *    
   * @param  mixed    $id     Flashcard id
   * @param  object   $oData  Flashcard answer data
   * @return boolean  True if update went succesfully
   */
  public static function putFlashcardData($id, $oData)
  {
    // sanitize JSON data
    if (!preg_match('/^[0-9]+$/', $id) ||
      ($id < 1 || $id > rtkBook::MAXKANJI_VOL3) ||
      !isset($oData->r) ||
      !preg_match('/^[1-3]$/', $oData->r) )
    {
      throw new coreException('updateFlashcard :: invalid data');
    }

    $user = coreContext::getInstance()->getUser();
    $userId = $user->getUserId();

    // get current review status

    $select = self::getInstance()
      ->select(array('totalreviews','leitnerbox','failurecount','successcount','lastreview'))
      ->where('framenum = ?', $id);
    self::filterByUserId($select, $userId)->query();
    $curData = self::$db->fetchObject();
    if (!$curData)
    {
      throw new coreException('updateFlashcard :: no record for id');
    }

    $oUpdateData = LeitnerSRS::rateCard($curData, $oData->r);
//echo '<p>UPDATE framenum '.$id.' with '.print_r($oUpdateData, true);
//return true;

    $result = self::getInstance()->update($oUpdateData, 'userid = ? AND framenum = ?', array($userId, $id));

    if ($result && $oData->r > 1)
    {
      // clear relearned kanji if successfull answer
      LearnedKanjiPeer::clearKanji($userId, $id);
    }

    return $result;
  }

  /**
   * Returns a timestamp localized to the user.
   * 
   * Currently, this timestamp corresponds to the adjusted flashcard's "lastreview" timestamp.
   * 
   * @return  int   Server's MySQL timestamp adjusted to user's local time.
   */
  public static function getLocalizedTimestamp()
  {
    $user = coreContext::getInstance()->getUser();

    $ts = self::$db->fetchOne('SELECT UNIX_TIMESTAMP(?)', new coreDbExpr($user->sqlLocalTime()));
    if (!$ts) {
      throw new coreException('getLocalizedTimestamp() failed');
    }
    
    return $ts;
  }

  /**
   * Returns Select object for the Review Summary page.
   * Select all flashcards reviewed since the given (localized) timestamp.
   * 
   * @see    getLocalizedTimestamp()
   * 
   * @param
   * @return
   */
  public static function getReviewSessionFlashcards($userId, $ts_start)
  {
    $select = self::getInstance()->select(array(
      'userid', self::getInstance()->getName().'.framenum', 'keyword', 'failurecount', 'successcount', 'leitnerbox',
      'ts_lastreview' => 'UNIX_TIMESTAMP(lastreview)', 'kanji',
      'onyomi', 'strokecount'))
      ->joinLeftUsing(KanjisPeer::getInstance()->getName(), 'framenum')
      ->where('UNIX_TIMESTAMP(lastreview) >= ?', $ts_start);
    $select = self::filterByUserId($select, $userId);
//DBG::out($select);exit;
    return $select;
  }

  /**
   * Return count of failed kanji for user.
   * 
   * @param
   * @return
   */
  public static function getRestudyKanjiCount($userId)
  {
    return self::getInstance()->count('userid = ? AND leitnerbox=1 AND totalreviews>0', $userId);
  }

  /**
   * Return select object for the Restudy Kanji list.
   * 
   * @param  integer   $userId
   * 
   * @return coreDatabaseSelect
   */
  public static function getAllRestudyKanjiSelect($userId)
  {
    $select = self::getInstance()
      ->select(array('reviews.framenum', 'keyword', 'successcount', 'failurecount', 'ts_lastreview' => 'UNIX_TIMESTAMP(lastreview)'))
      ->joinLeftUsing(KanjisPeer::getInstance()->getName(), 'framenum')
      ->where('leitnerbox=1 AND totalreviews>0');
    return self::filterByUserId($select, $userId);
  }

  /**
   * Return data for Restudy List "quick" view in the side column of the Study page.
   * 
   * Returns all failed kanji that are not currently in the learned list.
   * 
   * @param
   * 
   * @return array<array>  Resultset
   */
  public static function getRestudyKanjiList($userId)
  {
    $select = self::getInstance()
      ->select(array('reviews.framenum',  'keyword'))
      ->from('reviews')
      ->joinLeftUsing('learnedkanji', 'userid,framenum')
      ->join('kanjis', 'reviews.framenum = kanjis.framenum')
      ->where('reviews.userid = ?', $userId)
      ->where('leitnerbox=1 AND totalreviews>0')
      ->where('learnedkanji.framenum IS NULL')  /* not in the learned list */
      ->limit(10);
    
    /*
    $select = self::getInstance()->select(array('reviews.framenum', 'keyword'))
      ->joinLeftUsing(KanjisPeer::getInstance()->getName(), 'framenum')
      ->where('leitnerbox=1 AND totalreviews>0')
      ->limit(10);
    $select = self::filterByUserId($select, $userId);
    */
//echo $select;exit;

    return self::$db->fetchAll($select);
  }


  /**
   * Returns frame number for the first kanji in index order,
   * which is in the failed stack and not yet "learned".
   * 
   * @return mixed  Integer framenum, or false
   */
  public static function getNextUnlearnedKanji($userId)
  {
    $select = self::getInstance()
      ->select('reviews.framenum')
      ->from('reviews')
      ->joinLeftUsing('learnedkanji', 'userid,framenum')
      ->join('kanjis', 'reviews.framenum = kanjis.framenum')
      ->where('reviews.userid = ?', $userId)
      ->where('leitnerbox=1 AND totalreviews>0')
      ->where('learnedkanji.framenum IS NULL')  /* not in the learned list */
      ->limit(1);
    return self::$db->fetchOne($select);
  }


  /**
   * Checks if a kanji flashcard is "failed" (in the red stack/box 1)
   * 
   * @return boolean
   */
  public static function isRestudyKanji($userId, $frameNum)
  {
    self::getInstance()->select(array('leitnerbox', 'totalreviews'))
      ->where('framenum = ? AND userid = ?', array($frameNum, $userId))
      ->query();
    $r = self::$db->fetchObject();
    return (is_object($r) && $r->leitnerbox == 1 && $r->totalreviews > 0);
  }

  /**
   * Filter flashcard selection by frame number for given RtK Volume
   * (or no filter = all).
   * 
   * @param  coreDatabaseSelect $select
   * @param  string   $filter   'rtk1', 'rtk3', 'rtk1+3', '' (no filter)
   * 
   * @return coreDatabaseSelect   Returns modified select object
   */
  private static function filterByRtk($select, $filter = null)
  {
    switch ($filter)
    {
      case 'rtk1':
        $select->where('framenum <= ?', rtkBook::MAXKANJI_VOL1);
        break;
      case 'rtk3':
        $select->where('framenum > ? AND framenum <= ?', array(rtkBook::MAXKANJI_VOL1, rtkBook::MAXKANJI_VOL3));
        break;
      case 'rtk1+3':
        $select->where('framenum <= ?', rtkBook::MAXKANJI_VOL3);
        break;
      default:
        break;
    }

    return $select;
  }

  /**
   * Apply user id filter to select object.
   * 
   * @param  coreDatabaseSelect  $select
   * @param  int   $userId
   * 
   * @return coreDatabaseSelect
   */
  private static function filterByUserId(coreDatabaseSelect $select, $userId)
  {
    return $select->where('userid = ?', $userId);
  }
}
