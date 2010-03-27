<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * summary validation.
 * 
 * Checks required post variables sent by the uiFlashcardReview component
 * at the end of a review session.
 * 
 */

return array
(
  'fields' => array
  (
    'ts_start' => array
    (
      'required'        => array
      (
        'msg'           => 'Error'
      ),
      'CallbackValidator' => array
      (
        'callback'        => array('BaseValidators', 'validateInteger'),
        'invalid_error'   => 'Validation failed'
      )
    ),

    'fc_pass' => array
    (
      'required'        => array
      (
        'msg'           => 'Error'
      ),
      'CallbackValidator' => array
      (
        'callback'        => array('BaseValidators', 'validateInteger'),
        'invalid_error'   => 'Validation failed'
      )
    ),

    'fc_fail' => array
    (
      'required'        => array
      (
        'msg'           => 'Error'
      ),
      'CallbackValidator' => array
      (
        'callback'        => array('BaseValidators', 'validateInteger'),
        'invalid_error'   => 'Validation failed'
      )
    )
  )
);
