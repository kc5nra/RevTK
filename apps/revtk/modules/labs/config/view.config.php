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
    'title' => 'Index',
    'stylesheets' => array
    (
      '/css/2.0/widgets.min.css'
    ),

  ),
  
  'review' => array
  (
    'title'         => 'Labs Review',
    'stylesheets'   => array
    (
      '/revtk/labs-alpha-flashcardreview.juicy.css'
    ),
    'javascripts'   => array
    (
      '/revtk/labs-alpha-flashcardreview.juicy.js'
    )
  ),

  'all' => array
  (
    'title'             => 'Labs'
  )

);
