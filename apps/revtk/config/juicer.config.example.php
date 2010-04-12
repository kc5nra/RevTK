<?php
/**
 * Juicer configuration file for Reviewing the Kanji.
 *
 * Set the paths to point to local repositories and copy the file to "juicer.config.php"
 * in the same folder.
 *
 * Explanations:
 *   YUI2      Yahoo's YUI2 is the current javascript library in use in RevTK.
 *
 *   FRONT     This is meant to be the equivalent of the /lib folder, but for
 *             front end code. Any files in here can only be sourced through
 *             Juicer, since they are not in the server's public web/ folder!
 *
 *   WEB       This one points to the public web folder. For legacy code, it is
 *             easier to access files from there. Any image dependencies from
 *             YUI2 or FRONT will also be copied here.
 *
 *   MAPPINGS  This tells Juicer for asset dependencies such as images, where
 *             to copy them in the web folder. This keeps things neatly arranged
 *             and also in a predictable location.
 *
 * Installation:
 * - Download YUI2 "Full Developer Kit" from http://developer.yahoo.com/yui/2/
 *   and set the YUI2  paths accordingly (point them to the /build folder).
 *
 * For more documentation on Juicer, please see the project documentation at:
 *   http://github.com/fabd/Juicer
 *
 * Please note currently fabd/juicer is not maintained on Github, the latest
 * version of Juicer is maintained within RevTK. It should be a sub-module..
 *
 * @author  Fabrice Denis
 */

return array
(
  // Yahoo's YUI
  'YUI2'     => '/Users/faB/Development/Frameworks/yui_2.8.0r4/build',

  // All reusable front end code should eventually go there
  'FRONT'    => '/Users/faB/Sites/revtk/lib/front',

  // Include source for legacy front end code still living in the web/ folder
  'WEB'      => '/Users/faB/Sites/revtk/web',

  'MAPPINGS' => array
  (
    // Mapping YUI asset dependencies to the Web folder
    '/Users/faB/Development/Frameworks/yui_2.8.0r4/build' => 'yui2'
  )
);

