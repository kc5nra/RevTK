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
 * @author     Fabrice Denis
 * @package    RevTK
 * @subpackage About
 */

class aboutActions extends coreActions
{
	/**
	 * The About page.
	 * 
	 * @return 
	 */
	public function executeIndex()
	{
		$this->forward('about', 'about');
	}

	public function executeAbout()
	{
	}

	/**
	 * The Learn More page.
	 * 
	 * @return 
	 */
	public function executeLearnmore()
	{
	}
	
	/**
	 * The Acknowledgments page.
	 * 
	 * @return 
	 */
	public function executeAcknowledgments()
	{
	}

	/**
	 * The Donation page.
	 * 
	 * @return 
	 */
	public function executeSupport()
	{
	}
}
