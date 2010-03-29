<?php
/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010	Fabrice Denis
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
 *		boxes/*:
 *			boxesGet($boxes, $untestedCount)								GET boxes
 *			boxesIdGet($boxData)														GET boxes/{boxId}
 * 
 *		users/*:
 *			usersGet()																			GET users
 *			usersIdGet($user)																GET users/{userId}
 *
 *		rest/*:
*				restApiKeyGet($apiKey)													GET rest/apiKey
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
	 * Psuedo Schema:
	 * 
	 *	{ 
	 *		['response'] => STRING($apiKey)
	 *	}
	 *		
	 * @param $apiKey apiKey
	 * @return response 
	 **/
	public static function restApiKeyGet($apiKey)
	{
		$apiResponse		= null;
		
		// add to the response
		$apiResponse['response'] = $apiKey;
		
		return $apiResponse;
	}

	/**
	 * GET boxes
	 * 
	 * Psuedo Schema:
	 * 
	 *	{ 
	 *		['response'] => {
	 *			[0..n] = {
	 *				['expiredCards']	=> INTEGER
	 *				['freshCards']		=> INTEGER
	 *				['totalCards']		=> INTEGER
	 *			}
	 *			['untestedCount']		=> INTEGER
	 *		}
	 * @param $boxes							an array of boxes
	 * @param $untestedCardsTotal number of untested cards
	 * @return response 
	 **/
	public static function boxesGet($boxes, $untestedCount)
	{
		$apiResponse	= null;
		$newBoxHolder = null;
		
		$boxId = 0;
		foreach ($boxes as $box) {
			// temporary box, avoid value copy
			$box			= &$boxes[$boxId];
			
			// add what used to be the array index (boxId) as a child of the array
			// bump up each index by 1 (all sql statements re: boxes are 1 origin)
			$newBox['id']							= $boxId + 1; 
			$newBox['expiredCards']		= $box['expired_cards'];
			$newBox['freshCards']			= $box['fresh_cards'];
			$newBox['totalCards']			= $box['total_cards'];
			
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
	 * Psuedo Schema:
	 * 
	 *	{ 
	 *		['response'] => { 
	 *			[0..n] = {
	 *				['id']						=> INTEGER
	 *				['keyword']				=> STRING
	 *				['kanji']:				=> STRING
	 *				['onyomi']				=> STRING
	 *				['lessonNumber']	=> INTEGER
	 *				['strokeCount']		=> INTEGER
	 *		}
	 *	}
	 *		
	 * @param $boxData an array of cards in the box
	 * @return response 
	 **/
	public static function boxesIdGet($boxData)
	{
		$apiResponse		= null;
		$newCardHolder	= null;
		
		// loop through and create a new card
		foreach($boxData as &$card) {
			
			// copy relevant fields
			$newCard['id']						= $card['id'];
			$newCard['kanji']					= $card['kanji'];
			$newCard['onyomi']				= $card['onyomi'];
			$newCard['lessonNumber']	= $card['lessonnum'];
			$newCard['strokeCount']		= $card['strokecount'];
			
			// add the card to the card holder
			$newCardHolder[] = $newCard;
		}
		
		// add to the response
		$apiResponse['response'] = $newCardHolder;
		
		return $apiResponse;
	
	}
	
	/**
	 * GET users/{userId}
	 * 
	 * Psuedo Schema:
	 * 
	 *	{ 
	 *		['response'] => { 
	 *			['id']							=> INTEGER
	 *			['userName']				=> STRING
	 *			['joinDate']:				=> STRING
	 *			['lastLogin']				=> STRING
	 *			['location']				=> STRING
	 *			['timeZone']				=> INTEGER
	 *		}
	 *	}
	 *		
	 * @param $user an array containing a user's information
	 * @return response 
	 **/
	public static function usersIdGet($user)
	{
		$apiResponse		= null;

		// copy relevant fields
		$newUser['id']					= $user['userid'];
		$newUser['userName']		= $user['username'];
		$newUser['joinDate']		= $user['joindate'];
		$newUser['lastLogin']		= $user['lastlogin'];
		$newUser['location']		= $user['location'];
		$newUser['timeZone']		= $user['timezone'];
			
		// add to the response
		$apiResponse['response'] = $newUser;
		
		return $apiResponse;
	}

	/**
	 * Rest Exception, this is not called by anyone except api Exception classes.
	 * 
	 * Psuedo Schema:
	 * 
	 *	{ 
	 *		['error'] = {
	 *			['message']			=> STRING
	 *			['statusCode']	=> INTEGER
	 *		}
	 *	}
	 *		
	 * @param $apiKey apiKey
	 * @return response 
	 **/
	public static function restException($message, $statusCode)
	{
		$apiResponse					= null;
		$error['message']			= $message;
		$error['statusCode']	= $statusCode;
		
		// add to the error
		$apiResponse['error'] = $error;
		
		return $apiResponse;
	}

}
?>