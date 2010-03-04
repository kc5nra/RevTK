<?php
/**
 * RequestThrottler
 * 
 * Simple class to check a minimum time period between two requests
 * from the same user.
 * 
 * @package    
 * @author     Fabrice Denis
 */

class RequestThrottler
{
	const
		/**
		 * Minimum time between requests (cf. strtotime())
		 */
		TIMEOUT = '+10 seconds';
	
	protected
		$user      = null,
		$name      = '';

	/**
	 * 
	 * @return
	 */
	public function __construct($user, $id)
	{
		$this->user = $user;
		$this->name = 'request.throttle.'.$id;
	}


	/**
	 * Returns true if enough time has elapsed since last request,
	 * otherwise false to indicate the minimum time has not elapsed between
	 * requests.
	 * 
	 * @param coreUser $user
	 * @param string   $id     Request id can be a number or string
	 * 
	 * @return boolean
	 */
	public function isValid()
	{
		$waittime = $this->user->getAttribute($this->name, null);

		if (!is_null($waittime))
		{
			$nowtime = time();
		
			return ($nowtime >= $waittime);
		}
		
		return true;
	}

	/**
	 * Marks the current timestamp for a succesful request, necessary
	 * to throttle the following requests.
	 * 
	 * @param coreUser $user
	 * @param string   $id     Request id can be a number or string
	 * 
	 * @return 
	 */
	public function setTimeout()
	{
		$this->user->setAttribute($this->name, strtotime(self::TIMEOUT));
	}
}
