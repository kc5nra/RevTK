<?php
/**
 * Data model for the PunBB forum users table.
 * 
 * @todo  Add config parameter for the database name since this is a project level
 *        model. Add initialize() method to configure the table name.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

class PunBBUsersPeer extends coreDatabaseTable
{
  protected
    $tableName   = 'punbb1.users',
    $columns     = array(
		'id',
		'group_id',
		'username',
		'password',
		'email',
		'title',
		'realname',
		'url',
		'jabber',
		'icq',
		'msn',
		'aim',
		'yahoo',
		'location',
		'use_avatar',
		'signature',
		'disp_topics',
		'disp_posts',
		'email_setting',
		'save_pass',
		'notify_with_post',
		'show_smilies',
		'show_img',
		'show_img_sig',
		'show_avatars',
		'show_sig',
		'timezone',
		'language',
		'style',
		'num_posts',
		'last_post',
		'registered',
		'registration_ip',
		'last_visit',
		'admin_note',
		'activate_string',
		'activate_key'			
        );

	/**
	 * Get this peer instance to access the base methods.
	 * This function must be copied in each peer class.
	 */
	public static function getInstance()
	{
		return coreDatabaseTable::_getInstance(__CLASS__);
	}
	
	/**
	 * Fix the forum database name for the environment.
	 * 
	 * @return 
	 */
	public function initialize()
	{
		$this->tableName = coreConfig::get('app_forum_table_users');
	}
	
	/**
	 * Get the user's PunBB forum id,
	 * which can be used to build links to the PunBB forum.
	 * 
	 * @param  string $username
	 * @return mixed  Integer id, or false if user not found.
	 */
	public static function getForumUID($username)
	{
		$select = self::getInstance()->select('id')->where('username = ?', $username)->query();

		if ($row = self::$db->fetch())
		{
			return (int) $row['id'];
		}
		return false;
	}

	/**
	 * Compute path to PunBB forum root folder.
	 * 
	 * Requires config setting "app_path_to_punbb" (no trailing slash)
	 * 
	 * @return mixed  realpath to PunBB's root folder, or false if PunBB is not available
	 */
	public static function getPunBBRootPath()
	{
    $path_to_punbb = coreConfig::get('app_path_to_punbb');
    return ($path_to_punbb !== '') ? realpath(coreConfig::get('root_dir').'/'.$path_to_punbb) : false;
	}

	/**
	 * Create an accout for the user on the associated PunBB forum.
	 * 
	 * PunBB registration notes:
	 * 
	 * - We don't check for identical usernames here since all forum accounts
	 *   are created from the main site registration alone, and the main site
	 *   registration already did that check.
	 * - PunBB also checks for too similar usernames, we don't.
	 * - We don't check for "banned email address".
	 * - We don't check if "someone else already has registered with that e-mail address"
	 * 
	 * PunBB Config Cache:
	 * - Some default values come from the cache, it is assumed that the cache
	 *   is already available.
	 * 
	 * Registration informtion:
	 *   username
	 *   password
	 *   email
	 *   location
	 * 
	 * @param array $userinfo
	 * @return
	 */
	public static function createAccount($userinfo)
	{
		$pathToPunBB = self::getPunBBRootPath();
		
		// include PunBB files to avoid hard coding new forum user defaults
		// if the cached config file is not present yet, it will error out and throw an exception
		define('PUN', true);
		require($pathToPunBB.'/include/functions.php');
		require($pathToPunBB.'/include/email.php');
		require($pathToPunBB.'/cache/cache_config.php');

		$now = time();
		$unixtime_registered = $now;
		$unixtime_lastvisit  = $now;

		// get remote address
		$pathArray = coreContext::getInstance()->getRequest()->getPathInfoArray();
		$remote_addr = $pathArray['REMOTE_ADDR'];

		$data = array(
			'username'			=> $userinfo['username'],
			'group_id'  		=> $pun_config['o_default_user_group'],
			'password' 			=> pun_hash($userinfo['password']),
			'email' 			=> strtolower($userinfo['email']),
			'location' 			=> substr($userinfo['location'], 0, 30),   // max 30 chars
			'email_setting' 	=> intval(1),
			'save_pass' 		=> '0',
			'timezone' 			=> '0',
			'language' 			=> $pun_config['o_default_lang'],
			'style' 			=> $pun_config['o_default_style'],
			'registered' 		=> $unixtime_registered,
			'registration_ip' 	=> $remote_addr,
			'last_visit'		=> $unixtime_lastvisit
		);
	
//DBG::printr($data);
//return true;
		return self::getInstance()->insert($data);
	}

	/**
	 * Update record.
	 * 
	 * Array must contain keys matching table columns,
	 * values must be trimmed and validated already.
	 * 
	 * @param string  $username
	 * @param array   Assoc.array of row data
	 * @return
	 */
	public static function updateUser($username, $userdata)
	{
		$userid = self::getForumUID($username);
		return self::getInstance()->update($userdata, 'id = ?', $userid);
	}

	/**
	 * Update PunBB user password.
	 *
	 * @param string  $username
	 * @param string  $password  Raw password, to encode PunBB style.
	 */
	public static function setPassword($username, $raw_password)
	{
		// PunBB up to 1.2 uses sha1() to encode passwords in the database
		$hashed_password = sha1($raw_password);
		return self::updateUser($username, array('password' => $hashed_password));
	}

	/**
	 * Authenticate a user on the PunBB forums, by creating PunBB's cookie.
	 * 
	 * The code is based on PunBB's functions (pun_setcookie, pun_hash, ...)
	 * 
	 * We use RevTK's cookie expiration setting however (rtkUser::COOKIE_EXPIRE)
	 * 
	 * @param string  $username
	 * @param string  $password   Raw password needed for the PunBB cookie
	 * @param boolean $save_pass  This is the "rememberme" option
	 */
	public static function signIn($username, $password, $save_pass)
	{
		$user_id       = self::getForumUID($username);
		$password_hash = sha1($password);
		$expire        = $save_pass ? time() + rtkUser::COOKIE_EXPIRE : 0;
		
		self::setPunBBCookie($user_id, $password_hash, $expire);
	}

	/**
	 * Sign out user from the PunBB forum by clearing the PunBB cookie.
	 * 
	 * @return 
	 */
	public static function signOut()
	{
		// invalidate the login cookie (PunBB used a random_pass() function)
		self::setPunBBCookie(0, '12345678', time() - 3600);
	}

	/**
	 * Sets the PunBB forum cookie.
	 * 
	 * @see PunBB's pun_setcookie()
	 * 
	 */	
	public static function setPunBBCookie($user_id, $password_hash, $expire)
	{
		$pathToPunBB = self::getPunBBRootPath();

		// PunBB's settings for the forum cookie
		require($pathToPunBB.'/config.php');

		// see PunBB pun_setcookie()
		$cookie_value = urlencode(serialize(array($user_id, md5($cookie_seed.$password_hash))));

		coreContext::getInstance()->getResponse()->setCookie(
			$cookie_name, $cookie_value, $expire, $cookie_path, $cookie_domain, $cookie_secure, false);
	}

}
