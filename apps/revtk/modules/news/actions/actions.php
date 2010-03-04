<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 
 * 
 * @package    RevTK
 * @subpackage News
 * @author     Fabrice Denis
 */

class newsActions extends coreActions
{
	/**
	 * 
	 *
	 */
	public function executeIndex($request)
	{
		list($year, $month) = phpToolkit::array_splice_values($request->getParameterHolder()->getAll(), array('year', 'month'));

		if (!$year)
		{
			$this->newsPosts = SitenewsPeer::getMostRecentPosts();
			$this->title = 'News Archive <span>&raquo; Latest News</span>';
		}
		else if ($month>=1 && $month<=12)
		{
			$this->newsPosts = SitenewsPeer::getPostsByDate($year, $month);
			coreToolkit::loadHelpers('Date');
			$this->selection = format_date(mktime(0,0,0,$month,1,$year), "F Y");
			$this->title     = 'News for '.$this->selection;
		}
		else
		{
			$this->forward404();
		}
	}
	
	/**
	 * 
	 *
	 */
	public function executeDetail($request)
	{
		$this->forward404Unless($request->getParameter('id')>0, "This news post does not exist.");
		$this->post = SitenewsPeer::getPostById($request->getParameter('id'));
		$this->newsPosts = $this->post ? array($this->post) : array();
		$this->title = $this->post ? $this->post->subject : 'News post not found';
	}

}
