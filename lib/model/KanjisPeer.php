<?php
/**
 * Kanjis Peer.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class KanjisPeer extends coreDatabaseTable
{
  protected
    $tableName = 'kanjis',
    $columns = array
    (
      'keyword',
      'kanji',
      'onyomi',
      'framenum',
      'lessonnum',
      'strokecount'
    );

  /**
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

  /**
   * Return kanji data for given id (frame number).
   * 
   * Must sanitize kanji id.
   * 
   * @param   Integer  $id      Frame number (in the future will be unicode)
   * @return  Object   Kanji rowdata as object, or FALSE
   */
  public static function getKanjiById($id)
  {
    if (!BaseValidators::validateInteger($id)) {
      return false;
    }
    self::getInstance()->select()->where('framenum = ?', $id)->query();
    return self::$db->fetchObject();
  }

  /**
   * Return kanji data for given kanji as utf8 character.
   * 
   * @param   String   $utf8    Kanji character in utf8
   * @return  Object   Kanji rowdata as object, or FALSE
   */
  public static function getKanjiByCharacter($utf8)
  {
    if (empty($utf8)) {
      return false;
    }
    self::getInstance()->select()->where('kanji = ?', $utf8)->query();
    return self::$db->fetchObject();
  }

  /**
   * Return keyword only for given character.
   * 
   * Assumes framenum is already sanitized!
   * 
   * @todo   This is used in every story using the link feature.
   *         Could be optimized with an include file with array with all keywords.
   * 
   * @param
   * @return
   */
  public static function getKeyword($framenum)
  {
//    return 'xxx'.$framenum.'yyy';
    $select = self::getInstance()->select('keyword')->where('framenum = ?', $framenum);
    return self::$db->fetchOne($select);
  }

  /**
   * Returns formatted keyword if multiple editions, otherwise keyword as is
   * 
   * @param   String   $keyword   Single or multiple edition keyword as stored in the database
   * @return  String
   */
  public static function getDisplayKeyword($keyword)
  {
     if (strpos($keyword, rtkBook::EDITION_SEPARATOR) > 0)
     {
      return $keyword.'<br /><span class="edition">(multiple editions)</span>';
    }
    
    return $keyword;
  }

  /**
   * Returns flashcard data for the Kanji reviews.
   * This is a uiFlashcardReview callback, the data ($id) must be sanitized!
   *
   * @param
   * 
   * @return mixed   Object with flashcard data, or null
   */
  public static function getFlashcardData($id)
  {
    $id = (int)$id;

    // note: zero is not a valid kanji id
    if ($id < 1 || $id > rtkBook::MAXKANJI_VOL3) {
      return null;
    }

    $cardData = self::getKanjiById($id);

    if (!$cardData) {
      return null;
    }

    // set properties for the flashcard
    $cardData->id = $cardData->framenum;

    coreToolkit::loadHelpers(array('Tag', 'Url', 'Links'));
    $cardData->keyword = link_to_keyword($cardData->keyword, $cardData->framenum, 
      array('title' => 'Go to the Study page', 'target' => '_blank'));

    return $cardData;
  }

  /**
   * Search for a kanji by keyword.
   *
   * The search term should be an exact keyword or match the beginning of a keyword.
   *
   * The mutliple edition keyword separator (/) should be replaced with a wildcard (%) beforehand.
   *
   * The wildcard (%) can be used one or more times. A wildcard (%) is always added at the end
   * of the search term.
   *
   * @return  mixed   Frame number, or FALSE if no results.
   */
  public static function getFramenumForSearch($sSearch)
  {
    $s = trim($sSearch);
    //$s = preg_replace('/[^0-9a-zA-Z-\.\' \[\]\(\)]/', '', $s);
  
    if (CJK::hasKanji($s))
    {
      // it's not a western character..
      /* 0x3000 http://www.rikai.com/library/kanjitables/kanji_codes.unicode.shtml */
      
      self::getInstance()->select('framenum')->where('kanji = ?', $s)->query();
      
      return ($row = self::$db->fetchObject()) ? $row->framenum : false;
    }
    elseif (preg_match('/^[0-9]+$/', $s))
    {
      // check if frame number is valid
      $framenum = intval($s);
      return (self::getInstance()->count('framenum = ?', $framenum)) ? $framenum : false;
    }
    elseif (preg_match('/[^0-9]/', $s))
    {
      // search on keyword
      // acount for multiple edition keyword
      
      // try to find an exact match
      self::getInstance()->select('framenum')->where('keyword = ? OR keyword LIKE ? OR keyword LIKE ?', array($s, $s.'/%', '%/'.$s))->query();
      if ($row = self::$db->fetchObject())
      {
        return $row->framenum;
      }
      // minimum 3 characters for non-exact search to limit results
      elseif (strlen($s)<3)
      {
        return false;
      }
      // otherwise just pick the first match
      else
      {
        self::getInstance()->select('framenum')->where('keyword LIKE ?', $s.'%')->query();
        return ($row = self::$db->fetchObject()) ? $row->framenum : false;
      }
    }

    return false;
  }
}
