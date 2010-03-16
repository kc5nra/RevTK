<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple helper class returning timezones (part of the user account settings).
 * 
 * The current timezone list comes from the PunBB package.
 *
 * @package RevTK
 * @author  Fabrice Denis
 */

class rtkTimezones
{
  static $timezones = array(
    '-12' => '-12',
    '-11' => '-11',
    '-10' => '-10',
    '-9.5' => '-09.5',
    '-9' => '-09',
    '-8.5' => '-08.5',
    '-8' => '-08 PST',
    '-7' => '-07 MST',
    '-6' => '-06 CST',
    '-5' => '-05 EST',
    '-4' => '-04 AST',
    '-3.5' => '-03.5',
    '-3' => '-03 ADT',
    '-2' => '-02',
    '-1' => '-01',
    '0' => '00 GMT',
    '1' => '+01 CET',
    '2' => '+02',
    '3' => '+03',
    '3.5' => '+03.5',
    '4' => '+04',
    '4.5' => '+04.5',
    '5' => '+05',
    '5.5' => '+05.5',
    '6' => '+06',
    '6.5' => '+06.5',
    '7' => '+07',
    '8' => '+08',
    '9' => '+09',
    '9.5' => '+09.5',
    '10' => '+10',
    '10.5' => '+10.5',
    '11' => '+11',
    '11.5' => '+11.5',
    '12' => '+12',
    '13' => '+13',
    '14' => '+14'
  );
}
