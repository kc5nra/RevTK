<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sightreading page validation.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
  'fields' => array
  (
    'jtextarea' => array
    (
      'required'       => array
      (
        'msg'       => 'Please enter some japanese text.'
      ),
      'StringValidator'   => array
      (
        'max'       => 20000,
        'max_error'   => 'Text is too long (max 20000 characters).'
      )
    )
  )
);
