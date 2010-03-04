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
 * @subpackage Study
 * @author     Fabrice Denis
 */

class studyActions extends coreActions
{
	/**
	 * Study page.
	 * 
	 * @return 
	 */
	public function executeIndex($request)
	{
	}
	
	/**
	 * Study Page Search
	 * 
	 * Convert the search term to a framenum parameter and forward to index.
	 * 
	 * @url  /study/search/:search
	 *
	 */
	public function executeEdit($request)
	{
		$search = trim($request->getParameter('id', ''));
		if (!empty($search))
		{
			$search = CJK::normalizeFullWidthRomanCharacters($search);
	
			// update search box with cleaned up search term
			$request->setParameter('search', str_replace('_', '/', $search));
			
			$framenum = KanjisPeer::getFramenumForSearch($search);
		}

		if ($request->getMethod()===coreRequest::POST)
		{
			// Handle POST request from EditStory component.
			$this->forward404Unless(BaseValidators::validateInteger($framenum) && intval($framenum));
			
			// Learned kanji (doLearned.x, from input type="image")
			if ($request->hasParameter('doLearned_x'))
			{
				LearnedKanjiPeer::addKanji($this->getUser()->getUserId(), $framenum);

				// redirect to next restudy kanji
				$next = ReviewsPeer::getNextUnlearnedKanji($this->getUser()->getUserId());
				if ($next !== false)
				{
					$this->redirect('study/edit?id='.$next);
				}
			}
		}

		$request->setParameter('framenum', $framenum);

		if ($framenum)
		{
			$this->framenum = $framenum;
			$this->kanjiData = (object) KanjisPeer::getKanjiById($this->framenum);

			$this->getResponse()->setTitle('Study: '.$this->kanjiData->kanji.' "'.$this->kanjiData->keyword.'"');
		}
		else
		{
			$this->framenum = false;
		}
	}

	/**
	 * Clear the restudied kanji selection.
	 * 
	 * @return 
	 */
	public function executeClear($request)
	{
		LearnedKanjiPeer::clearAll($this->getUser()->getUserId());
	}


	/**
	 * Failed Kanji List.
	 * 
	 * @return 
	 */
	public function executeFailedlist($request)
	{
	}

	/**
	 * Failed Kanji List ajax table.
	 * 
	 * @return 
	 */
	public function executeFailedlisttable($request)
	{
		return $this->renderComponent('study', 'FailedListTable');
	}

	/**
	 * My Stories page.
	 * 
	 * @return 
	 */
	public function executeMystories($request)
	{
		$sortkey = $request->getParameter('sort', false);
		$this->forward404Unless(!$sortkey || preg_match('/^[a-z]+$/', $sortkey));

		// uiDataFilter
		$this->sort_links = array(
			'framenum' => array(
				'name'         => 'Frame#',
				'internal_uri' => 'study/mystories',
				'options'      => array('query_string' => 'sort=framenum',
													'title' => 'Order by RTK index number')
			),
			'keyword' => array(
				'name'         => 'Keyword',
				'internal_uri' => 'study/mystories',
				'options'      => array('query_string' => 'sort=keyword',
													'title' => 'Order by keyword')
			),
			'lastedit' => array(
				'name'         => 'Last Edit',
				'internal_uri' => 'study/mystories',
				'options'      => array('query_string' => 'sort=lastedit',
													'title' => 'Order by last time the story was edited')
			),
			'votes' => array(
				'name'         => 'Votes',
				'internal_uri' => 'study/mystories',
				'options'      => array('query_string' => 'sort=votes',
													'title' => 'Order by number of stars')
			)
		);

		if (!$sortkey || !isset($this->sort_links[$sortkey]))
		{
			$sortkey = 'lastedit';
		}

		$this->sort_active = $sortkey;
	}

	/**
	 * My Stories ajax component.
	 * 
	 * @return 
	 */
	public function executeMyStoriesTable($request)
	{
		return $this->renderComponent('study', 'MyStoriesTable');
	}

	/**
	 * Export user's stories to CSV.
	 * 
	 * Note! 'col_escape' option must match the select from StoriesPeer::getSelectForExport()
	 *
	 */
	public function executeExport($request)
	{
		$response = $this->getResponse();
		$response->setContentType('text/plain; charset=utf-8');

		$throttler = new RequestThrottler($this->getUser(), 'study.export');
		
		if (!$throttler->isValid())
		{
		//	$response->setContentType('text/plain; charset=utf-8');
			$response->setContentType('html');
			return $this->renderPartial('misc/requestThrottleError');
		}

		$csv = new ExportCSV($this->getContext()->getDatabase());
		$select = StoriesPeer::getSelectForExport($this->getUser()->getUserId());
		$csvText = $csv->export($select, array('framenum', 'kanji', 'keyword', 'public', 'last_edited', 'story'), array('col_escape' => array(0, 0, 1, 0, 0, 1)));
	
		$throttler->setTimeout();
		
		$response->setHttpHeader('Cache-Control', 'no-cache, must-revalidate');
		$response->setHttpHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$response->setHttpHeader('Content-Disposition', 'attachment; filename="my_stories.csv"');

		$this->setLayout(false);

		return $this->renderText($csvText);
	}

	/**
	 * EditStoryComponent ajax handler.
	 * 
	 * Request parameters:
	 * 
	 *   reviewMode   True if used from the Review page EditStory window
	 * 
	 * @return 
	 */
	public function executeEditstory($request)
	{
		$framenum = $request->getParameter('framenum', false);
		if (!BaseValidators::validateInteger($framenum)) {
			throw new rtkAjaxException('Bad request.');
		}

		$reviewMode = $request->hasParameter('reviewMode');

		$kanjiData = (object) KanjisPeer::getKanjiById($framenum);

		return $this->renderComponent('study', 'EditStory', array('framenum' => $framenum, 'kanjiData' => $kanjiData, 'reviewMode' => $reviewMode));
	}

	/**
	 * Ajax handler for Shared Stories component.
	 * 
	 * uid & sid identify the story to vote/report/copy.
	 * 
	 * Post:
	 * 
	 *   request     "star": star story
	 *               "report": report story
	 *               "copy": copy story
	 *   uid         User id of the Story's author
	 *   sid         Story id (framenum)
	 * 
	 * @return 
	 */
	public function executeAjax($request)
	{
		if ($request->getMethod()===coreRequest::GET)
		{
			// reload component
			$framenum = $request->getParameter('framenum', false);
			if (!BaseValidators::validateInteger($framenum)) {
				throw new rtkAjaxException('Bad request.');
			}
			
			$kanjiData = (object) KanjisPeer::getKanjiById($framenum);
			return $this->renderComponent('study', 'SharedStories', array('framenum' => $framenum, 'kanjiData' => $kanjiData));

		}
		else
		{
			$sRequest = $request->getParameter('request', '');
			$sUid = $request->getParameter('uid');
			$sSid = $request->getParameter('sid');
			
			if (!preg_match('/^(star|report|copy)$/', $sRequest)
				|| !BaseValidators::validateInteger($sUid)
				|| !BaseValidators::validateInteger($sSid))
			{
				throw new rtkAjaxException('Badrequest');
			}
	
			if ($sRequest==='copy')
			{
				// get unformatted story with original tags for copy story feature
				$oStory = StoriesPeer::getStory($sUid, $sSid);
				if ($oStory) {
					$oJSON = new stdClass;
					$oJSON->text = $oStory->text;
					return $this->renderText(coreJson::encode($oJSON));
				}
			}
			elseif ($sRequest==='star')
			{
				$oJSON = StoryVotesPeer::starStory($this->getUser()->getUserId(), $sUid, $sSid);
				return $this->renderText(coreJson::encode($oJSON));
			}
			elseif ($sRequest==='report')
			{
				$oJSON = StoryVotesPeer::reportStory($this->getUser()->getUserId(), $sUid, $sSid);
				return $this->renderText(coreJson::encode($oJSON));
			}
		}
		
		throw new rtkAjaxException('Badrequest');
	}
}
