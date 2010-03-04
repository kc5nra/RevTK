<?php
/**
 * LearnedKanji Peer.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class LearnedKanjiPeer extends coreDatabaseTable
{
	protected
		$tableName = 'learnedkanji',
    $columns = array
		(
			'userid',
			'framenum'
		);

	/**
	 * This function must be copied in each peer class.
	 */
	public static function getInstance()
	{
		return coreDatabaseTable::_getInstance(__CLASS__);
	}

	/**
	 * Returns count of relearned kanji for given user.
	 * 
	 * @return mixed  Count, or FALSE on failure
	 */
	public static function getCount($userId)
	{
		return self::getInstance()->count('userid = ?', $userId);		
	}

	/**
	 * 
	 * @return boolean
	 */
	public static function addKanji($userId, $frameNum)
	{
		return self::$db->query('REPLACE INTO '.self::getInstance()->getName().' (userid, framenum) VALUES (?, ?)',
			array($userId, $frameNum));
	}

	/**
	 * 
	 * @return
	 */
	public static function hasKanji($userId, $frameNum)
	{
		return self::getInstance()->count('userid = ? AND framenum = ?', array($userId, $frameNum)) > 0;
	}

	/**
	 * Remove a relearned kanji from the selection.
	 * 
	 * @return boolean
	 */
	public static function clearKanji($userId, $frameNum)
	{
		return self::getInstance()->delete('userid = ? AND framenum = ?', array($userId, $frameNum));
	}

	/**
	 * Clear the relearned kanji list for this user.
	 * 
	 * @return boolean
	 */
	public static function clearAll($userId)
	{
		return self::getInstance()->delete('userid = ?', $userId);
	}
}
