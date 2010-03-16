<?php
/**
 * LabsPeer
 * 
 * Currently represents several tables from the defunct Trinity alpha,
 * which are used in the labs/experimental features. Eventually separate
 * peer classes should be created for each of those tables..
 * 
 * Because this doesn't represent a single table, use
 *   self::$db->query() or self::$db->select()
 * and always specify the table name.
 *
 * See data/schemas/trinity_schema.sql
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class LabsPeer extends coreDatabaseTable
{
  protected
    $tableName = 'jdict',
    $columns = array
    (
      'dictid'
    );

  /**
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

  /**
   * $id starts at 1 !
   * @return boolean
   */
  public static function getFlashcardData($id)
  {
    $sess = coreContext::getInstance()->getUser()->getAttribute('uifr');
    if (!$sess) {
      throw new coreException('No session?');
    }
    if (isset($sess[$id-1]))
    {
      $card = (object)$sess[$id-1];   //new stdClass();
      $card->id = $id; // for uiFlashcardReview.js
      // set properties for the flashcard
      return $card;
    }
    return null;
  }
}
