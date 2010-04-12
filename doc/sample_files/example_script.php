<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sample file that can be used to start new scripts based on Command_CLI.
 *
 * Example usage:
 *   $ php doc/sample_files/example_script.php
 *
 * Edit $cmdHelp to set the command line help text.
 *
 * @author  Your Name
 */

require_once('lib/batch/Command_CLI.php');

class ExampleScript_CLI extends Command_CLI
{
  /**
   * Description of the command line tool, including list of flags.
   *
   * Below is an example, you should override it in the extending class.
   *
   * This array of data will be formatted in a consistent way for all command
   * line tools. The verbose flag (--v) will always be added at the end of the list
   * of flags.
   *
   */
  protected
    $cmdHelp = array(
      'short_desc' => 'Example file to create command line tools.',
      'usage_fmt'  => '[--v] --foo <file> [--bar]',
      'options'    => array(
        'foo <file>'     => "Description of the option flag. Descriptions spanning multiple\nlines can use the newline character.",
        'bar'            => 'Enable bar mode (defaults to "baz").'
      )
    );

  public function init()
  {
    parent::init();
    
    $this->verbose('Done!');
  }

}

$cmd = new ExampleScript_CLI();
$cmd->init();

