<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * User profile, community features(someday).
 * 
 * @package    RevTK
 * @subpackage profile
 * @author     Fabrice Denis
 */

class profileActions extends coreActions
{
	public function executeIndex($request)
	{
		$username = $request->getParameter('username');

		if (!$username)
		{
			if ($this->getUser()->isAuthenticated())
			{
				$username = $this->getUser()->getUserName();
			}
			else
			{
				// if unauthenticated user checks his (bookmarked?) profile, go to login and back
				$url = $this->getController()->genUrl('profile/index', true);
				$this->getUser()->redirectToLogin(array('referer' => $url));
			}
		}

		if ($user = UsersPeer::getUser($username))
		{
			$this->user = $user;
			$this->self_account = $user['username'] == $this->getUser()->getUserName();

      $this->kanji_count = ReviewsPeer::getReviewedFlashcardCount($user['userid']);
      $this->total_reviews = ReviewsPeer::getTotalReviews($user['userid']);
      
      $this->forum_uid = (coreConfig::get('app_path_to_punbb') !== null) ? PunBBUsersPeer::getInstance()->getForumUID($username) : false;
			
			return coreView::SUCCESS;
		}

		return coreView::ERROR;
	}
}
