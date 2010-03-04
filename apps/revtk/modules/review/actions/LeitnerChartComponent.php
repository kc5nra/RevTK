<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * LeitnerChart Component.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class LeitnerChartComponent extends coreComponent
{
	public function execute($request)
	{
		$user_id = coreContext::getInstance()->getUser()->getUserId();

		$this->filter = $this->getUser()->getLocalPrefs()->get('review.graph.filter', '');

		$carddata = ReviewsPeer::getLeitnerBoxCounts($this->filter);

		$this->restudy_cards = $carddata[0]['expired_cards'];

		// count untested cards and add to graph
		$this->untested_cards = ReviewsPeer::getCountUntested($user_id, $this->filter);

		$carddata[0]['fresh_cards'] = $this->untested_cards;
		$carddata[0]['total_cards'] += $this->untested_cards;
		
		// count totals (save a database query)
		//$this->total_cards = 0;
		$this->expired_total = 0;
		for ($i = 0; $i < count($carddata); $i++)
		{
			$box =& $carddata[$i];
			//$this->total_cards += $box['total_cards'];
			
			// count expired cards, EXCEPT the red stack
			if ($i > 0)
			{
				$this->expired_total += $box['expired_cards'];
			}
		}

		$this->chart_data = $this->makeChartData($carddata);

		
//DBG::printr($this->chart_data);exit;

		$this->me = $this;

		return coreView::SUCCESS;
	}

	/**
	 * Format data for the client-side javascript that builds a SVG chart.
	 * 
	 */
	protected function makeChartData($carddata)
	{
		$data = new stdClass();

		$boxes = array();		
		for ($i = 0; $i < count($carddata); $i++)
		{
			$stacks = array();
			
			// left stack
			$stacks[] = array(
			 	'value' => $carddata[$i]['expired_cards'],
				'type'  => $i == 0 ? 'failed' : 'expired'
			);
			
			// right stack
			$stacks[] = array(
			 	'value' => $carddata[$i]['fresh_cards'],
				'type'  => $i ==0 ? 'untested' : 'fresh'
			);

			$boxes[] = $stacks;
		}
		
		$data->boxes = $boxes;

		// links used in the chart
		coreToolkit::loadHelpers(array('Url'));
		$data->url_study  = url_for('study/failedlist'); //.'?'.http_build_query(array());
		$data->url_new    = $this->getReviewUrl(array('type' => 'untested'));
		$data->url_review = $this->getReviewUrl(array('type' => 'expired'));

		return $data;
	}

	/**
	 * Merge query parameters for the review graph mode.
	 * 
	 * Add the view filter param (all, rtk1, rtk3, ...)
	 * 
	 * @param  array  $query_params
	 * 
	 * @return array
	 */
	protected function getFilterParams($query_params)
	{
		if ($this->filter !== '')
		{
			$query_params['filt'] = $this->filter;
		}
		return $query_params;
	}

	/**
	 * Returns url for the review page with given query parameters.
	 * 
	 * @param  array   $params    Query parameters
	 * 
	 * @return string  HTML for link tag
	 */
	public function getReviewUrl($params)
	{
		return url_for('@review').'?'.http_build_query($this->getFilterParams($params));
	}
	
	/**
	 * Returns html link to review page with given query parameters.
	 * 
	 * Parameters specified as for link_to() helper.
	 * 
	 * @return string  HTML for link tag
	 */
	public function getReviewLink($name, $params, $options = array())
	{
		$options['query_string'] = http_build_query($this->getFilterParams($params));

		return link_to($name, '@review', $options);
	}
}


