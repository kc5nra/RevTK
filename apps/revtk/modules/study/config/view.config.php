<?php
/**
 * View configuration file for all actions in this module.
 * 
 * @package    RevTK
 * @author     Fabrice Denis
 */

return array
(
  'index' => array
  (
    'title'        => 'Study: Introduction'
  ),
  
  'edit' => array
  (
    'title'        => 'Index'
  ),
  
  'failedlist' => array
  (
    'title'        => 'Study: Failed Kanji List',

    'stylesheets' => array
    (
      '/css/2.0/widgets.css'
    ),
    'javascripts' => array
    (
      '/js/ui/widgets.min.js'
    )
  ),
  
  'mystories' => array
  (
    'title'        => 'Study: My Stories List',

    'stylesheets' => array
    (
      '/css/2.0/widgets.css'
    ),
    'javascripts' => array
    (
      '/js/ui/widgets.min.js'
    )
  ),
  
  'all' => array
  (
    'javascripts' => array
    (
      '/revtk/study-base.juicy.js'
    )
  )
);
