<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Login validation.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
  'fields' => array
  (
    'username' => array
    (
      'required'       => array
      (
        'msg'       => 'Please enter username.'
      ),
      'StringValidator'   => array
      (
        'min'       => 5,
        'min_error'   => 'Username is too short (min 5 characters).',
        // Note: PunBB username max length is 25
        'max'       => 25,
        'max_error'   => 'Username is too long (max 25 characters).'
      ),
      'CallbackValidator' => array
      (
        'callback'    => array('rtkValidators', 'validateUsernameChars'),
        'invalid_error' => 'Username: please use only letters, digits and underscore characters, no spaces.'
      ),
      'CallbackValidator' => array
      (
        'callback'    => array('rtkValidators', 'validateUsernamePrefix'),
        'invalid_error' => 'Username: no digits prefix, no underscore prefix/suffix, no double underscores.'
      )
    ),
    'password' => array
    (
      'required'       => array
      (
        'msg'       => 'Please enter password.'
      ),
      'RegexValidator'    => array
      (
        'match'     => true,
        'pattern'     => '/^[\x20-\x7e]+$/',
        'match_error'   => 'Password: please use only <a target="_blank" href="http://en.wikipedia.org/wiki/ASCII#ASCII_printable_characters">ASCII printable characters</a>.'
      )
    )
  )
);
