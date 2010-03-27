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
    'title'        => 'Review Status',
    'stylesheets' => array
    (
      '/revtk/review-home.juicy.css'
    ),
    'javascripts' => array
    (
      '/revtk/review-home.juicy.js'
    )
  ),

  'flashcardlist' => array
  (
    'title'        => 'Detailed flashcard list',
    'stylesheets' => array
    (
      '/css/2.0/widgets.css'
    )
  ),
  
  'review' => array
  (
    'title'        => 'Flashcard Review',
    'stylesheets' => array
    (
      '/revtk/kanji-flashcardreview.juicy.css'
    ),
    'javascripts' => array
    (
      '/revtk/kanji-flashcardreview.juicy.js'
    )
  ),

  'summary' => array
  (
    'title'        => 'Review Summary',
    
    'stylesheets' => array
    (
      '/css/2.0/widgets.css'
    ),
    'javascripts' => array
    (
      '/js/lib/prototype.min.js',
      '/js/ui/uibase.min.js',
      '/js/ui/widgets.min.js'
    )
  ),
  
  'fullscreen' => array
  (
    'title'        => 'Flashcard Review',
    'stylesheets' => array
    (
    ),
    'javascripts' => array
    (
      '/js/lib/prototype.min.js'
    )
  )
);
