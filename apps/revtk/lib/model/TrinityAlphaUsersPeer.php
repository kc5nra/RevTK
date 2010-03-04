<?php
/**
 * TrinityAlphaUsers Peer.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class TrinityAlphaUsersPeer extends coreDatabaseTable
{
	protected
		$tableName = 'users_trinity',
    $columns = array
		(
			'userid',
			'allowed'
		);

	/**
	 * This function must be copied in each peer class.
	 */
	public static function getInstance()
	{
		return coreDatabaseTable::_getInstance(__CLASS__);
	}

	/**
	 * Returns true if user is registered in the Trinity Alpha.
	 * 
	 * @param  
	 * @return 
	 */
	public static function isUserRegistered($userId)
	{
		return (boolean) self::getInstance()->count('userid = ?', $userId);
	}
}
