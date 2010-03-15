<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Edit Account validation.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
  'fields' => array
  (
    'email' => array
    (
      'required'       => array
      (
        'msg'       => 'Email is required.'
      ),
      'EmailValidator'   => array
      (
        'strict'     => true,
        'email_error'   => 'Email is not valid.'
      ),
      'StringValidator'   => array
      (
        'min'       => 7,
        'min_error'   => 'Email is too short (min 7 characters).',
        'max'       => 50,
        'max_error'   => 'Email is too long (max 50 characters).'
      )
    ),
    'location' => array
    (
      // 30 characters is the PunBB forum limit
      'StringValidator'   => array
      (
        'max'       => 30,
        'max_error'   => 'Location is too long (max 30 characters).'
      ),
      'RegexValidator'   => array
      (
        'match'     => true,
        'pattern'     => '/^([a-zA-Z0-9])+([a-zA-Z0-9 \'-])*$/',
        'match_error'   => 'Location: only letters and digits, spaces, single quotes or dashes.'
      )
    )
  )
);
