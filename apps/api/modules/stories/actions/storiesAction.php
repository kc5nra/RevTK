<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handle the stories REST actions.
 *
 * @author John Bradley
 * @version 1
 * @copyright Turret Technology, LLC., 10 April, 2010
 * @package api
 * @subpackage stories
 **/

class storiesAction extends apiAction
{
  public function execute($request)
  {
    $this->validateRequestAndSetUser();

    switch($request->getMethod()) {
      default: {
        $e = new apiRestException('You are not allowed to execute actions on the Stories object.');
        $e->setStatusCode(403);
        throw $e;
      }
    }
  }
}

?>