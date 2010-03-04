<?php
/**
 * Sitenews Peer.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class SitenewsPeer extends coreDatabaseTable
{
	protected
		$tableName = 'sitenews',
        $columns = array
		(
			'id',
			'subject',
			'text',
			'created_on',
			'updated_on'
		);

	/**
	 * This function must be copied in each peer class.
	 */
	public static function getInstance()
	{
		return coreDatabaseTable::_getInstance(__CLASS__);
	}

	/**
	 * Return single formatted post by Id
	 * 
	 * @param  int     $id     Id of post
	 * @param  boolean $bried  Cut long post (see formatPost)
	 * @return object  Single  formatted post, or false if Id is not found
	 */
	public static function getPostById($id, $brief = false)
	{
		$select = self::getInstance()->select(array('id', 'date' => 'DATE_FORMAT(created_on,\'%e %M %Y\')', 'subject', 'text'))
			->where('id = ?', $id)->query();
		
		if ($post = self::$db->fetchObject())
		{
			$posts = self::formatPostsArray(array($post), $brief);
			return $posts[0];
		}
		return false;
	}

	/**
	 * Get array of posts using year, month and day as search criteria.
	 * 
	 * Returns array of posts in DESCENDING order of creation date.
	 * 
s	 * @return array<Object>  Array of posts, empty array if no matches
	 */
	public static function getPostsByDate($year, $month = 0, $day = 0)
	{
		$select = self::getInstance()->select(array('id', 'date' => 'DATE_FORMAT(created_on,\'%e %M %Y\')', 'subject', 'text'))
			->order('created_on DESC')
			->where('EXTRACT(YEAR FROM created_on) = ?', $year);
		if ($month)
		{
			$select->where('EXTRACT(MONTH FROM created_on) = ?', $month);
		}
		if ($day)
		{
			$select->where('EXTRACT(DAY FROM created_on) = ?', $day);
		}

		$fetchMode = self::$db->setFetchMode(coreDatabase::FETCH_OBJ);
		$result = self::formatPostsArray(self::$db->fetchAll($select));
		self::$db->setFetchMode($fetchMode);
		return $result;
	}

	/**
	 * Return most recent posts, with 'brief' on.
	 * 
	 * @param  integer[Optional]  Max number of posts to return
	 * @return array<Object>      Formatted posts
	 */
	public static function getMostRecentPosts($max = 5)
	{
		$select = self::getInstance()->select(array('id', 'date' => 'DATE_FORMAT(created_on,\'%e %M %Y\')', 'subject', 'text' ))
			->order('created_on DESC')
			->limit($max);

		$fetchMode = self::$db->setFetchMode(coreDatabase::FETCH_OBJ);
		$result = self::formatPostsArray(self::$db->fetchAll($select), true);
		self::$db->setFetchMode($fetchMode);
		return $result;
	}

	/**
	 * Get total of news posts for each year/month, for the News Archive index.
	 * 
	 * @param
	 * @return array<Object>
	 */
	public static function getArchiveIndex()
	{
		$select = self::getInstance()->select(array(
			'count' => 'COUNT(*)',
			'yyyymm'=> 'DATE_FORMAT(created_on, \'%Y%m\')',
			'year'  => 'EXTRACT(YEAR FROM created_on)',
			'month' => 'EXTRACT(MONTH FROM created_on)'))
			->group('yyyymm')
			->order('yyyymm DESC');
		$fetchMode = self::$db->setFetchMode(coreDatabase::FETCH_OBJ);
		$result = self::$db->fetchAll($select);
		self::$db->setFetchMode($fetchMode);
		return $result;
	}

	/**
	 * 
	 * 
	 * @param  string $text    Raw post text from database, with special markup
	 * @param  bool   $brief   Cut long post at the '<more>' mark and add a link to the full post
	 * @param  int    $msg_id  Id of the news post if $brief is True
	 * @return string
	 */
	public static function formatPost($text, $brief = false, $msg_id = 0)
	{
		coreToolkit::loadHelpers(array('Tag', 'Url'));

		// brief mode
		if (($pos = strpos($text, '<more>')))
		{
			if ($brief)
			{
				$text = substr($text, 0, $pos);
				$text .= link_to('Continued...', 'news/detail?id='.$msg_id, array('class' => 'readmore'));
			}
			else
			{
				$text = preg_replace('/(\r\n?)*<more>(\r\n?)*/', '<p>', $text);
			}
		}
	
		// replace linefeeds by XHTML <br />
		$s = preg_replace('/(\r\n?)*(<\/?\w+>)(\r\n?)*/', '$2', $text);
		$s = preg_replace('/(\r\n?)+/', '<br /><br />', $s);
		return $s;
	}
	
	/**
	 * Format the text in array of post(s) fetched from database.
	 * 
	 * @param  array<Object>  Array of posts
	 * @param  boolean        See formatPost()
	 * @return array<Object>  Array of posts
	 */
	public static function formatPostsArray(array $posts, $brief = false)
	{
		foreach ($posts as $post)
		{
			// the post id is used for the "read more..." link when $brief is True
			$post->text = self::formatPost($post->text, $brief, $post->id);
		}
		return $posts;
	}

}
