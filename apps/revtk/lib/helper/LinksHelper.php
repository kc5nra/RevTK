<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Helpers to create links to application resources.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

/**
 * Creates a link to a member's profile page.
 * 
 * @param  string    $username   Username
 * @param  array     $options    Optional attributes for the link, see link_to()
 */
function link_to_member($username, $options = array())
{
  $internal_uri = '@profile?username='.$username;
  return link_to($username, $internal_uri, $options);
}

/**
 * This helper creates a link to the Study area page for a given kanji.
 * 
 * @param string  $sKeyword   The label
 * @param mixed   $sKanjiId   Kanji id is a kanji character, framenum or keyword
 */
function link_to_keyword($sKeyword, $sKanjiId = '', $options = array())
{
  if ($sKanjiId === '') {
    $sKanjiId = $sKeyword;
  }
  
  return link_to($sKeyword, '@study_edit?id='.$sKanjiId, $options);
}
