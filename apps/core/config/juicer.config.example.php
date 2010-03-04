<?php
/**
 * Juicer configuration file.
 * 
 * Set the paths to point to local repositories and copy the file to "juicer.config.php"
 * 
 * @author  Fabrice Denis
 */

return array
(
  'CORE'     => '/Path/To/RevTK/lib/front/corejs',
								
	'YUI3'     => '/Path/To/Frameworks/yui_3.0.0/build',

  'MAPPINGS' => array
  (
		// Map the YUI3 build path to web/yui3
    '/Path/To/Frameworks/yui_3.0.0/build'  => 'yui3',
    
    // Map the coreJs path to web/corejs
    '/Path/To/RevTK/lib/front/corejs'    => 'corejs'
  )
);
