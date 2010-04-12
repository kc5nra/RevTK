<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Add a user to the database.
 * 
 * Usage:
 * 
 *   $ php batch/maintenance/createUser.php --username "<username>" --password "<password>"
 *
 * Optional
 *   --email "johndoe@foobar.com"
 *   --location "Canada"
 *   --level <1-9>    (defaults to 1 = USERLEVEL_USER, 9 = USERLEVEL_ADMIN, cf. UsersPeer)
 * 
 * WARNING!
 * - The Command_CLI default app and environment (revtk/dev) determines the database connection!
 * - Does not validate against the website's password restrictions! (todo)
 *
 * @author  Fabrice Denis
 */

require_once('lib/batch/Command_CLI.php');

class CreateUser_CLI extends Command_CLI
{
  protected
    $cmdHelp = array(
      'short_desc' => 'Add a user to the database.',
      'usage_fmt'  => '--username "<username>" --password "<password>"',
      'options'    => array(
        'email "<email>"'           => 'Sets the email address (optional)',
        'location "City, Country"'  => 'Sets the user\'s location (shows in the members list, optional)',
        'level <1-9>'               => 'Sets user level. Defaults to 1 (USERLEVEL_USER). 9 is USERLEVEL_ADMIN (see UsersPeer)'
      )
    );


  public function init()
  {
    parent::init();
    
// Verify we're on the correct database
//print_r(coreConfig::get('database_connection'));exit;
    
    $connectionInfo = coreConfig::get('database_connection');
    $this->verbose("Using database: %s", $connectionInfo['database']);

    $username = trim($this->getOption('username'));
    $raw_password = trim($this->getOption('password'));

    if (empty($username) || empty($raw_password)) {
      $this->throwError('Username or password is empty.');
    }

    if (UsersPeer::usernameExists($username)) {
      $this->throwError('That username already exists, foo!');
    }

    $this->createUser($username, $raw_password);

    $this->verbose('Success!');
  }

  private function createUser($username, $raw_password)
  {
    $userinfo = array(
      'username' => $username,
      'password' => coreContext::getInstance()->getUser()->getSaltyHashedPassword($raw_password),
      'userlevel'=> $this->getOption('level', UsersPeer::USERLEVEL_USER),
      'email'    => trim($this->getOption('email', 'created@localhost')),
      'location' => trim($this->getOption('location', 'localhost'))
    );
    
    //die(print_r($userinfo, true));

    if (false === UsersPeer::createUser($userinfo)) {
      $this->throwError('Could not create user.');
    }

  }
}

$cmd = new CreateUser_CLI();
$cmd->init();
