<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Edit Story validation.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
	'fields' => array
	(
		'txtStory' => array
		(
			'StringValidator' 	=> array
			(
				'max' 			=> 512,
				'max_error' 	=> 'Story is too long (max. 512 characters).'
			)
		)
	)
);
