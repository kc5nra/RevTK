<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Forgot Password validation.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
  'fields' => array
  (
    'email_address' => array
    (
      'required'         => array
      (
        'msg'           => 'Email address is required.'
      ),
      'EmailValidator'  => array
      (
        'strict'        => true,
        'email_error'   => 'Email is not valid.'
      )
      
      /*
      'StringValidator'   => array
      (
        'min'       => 5,
        'min_error'   => 'Username is too short (min 5 characters).',
        // Note: PunBB username max length is 25
        'max'       => 25,
        'max_error'   => 'Username is too long (max 25 characters).'
      )
      */
    )
  )
);
