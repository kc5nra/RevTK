/**
 * FlashcardReview Common Bundle
 *
 * This is version 1.0: the "deprecated" prototypejs based implementation, for compatiblity
 * with the old codebase.
 *
 * Juicer build:
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/revtk/labs-alpha.juicy.js
 * 
 * Minification:
 *  java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar web/revtk/labs-alpha.juiced.js -o web/revtk/labs-alpha.min.js
 *   
 * @package RevTK
 * @author  Fabrice Denis
 * @date    March 2010
 */

/* =require from "%YUI2%" */
/* =require "/yahoo-dom-event/yahoo-dom-event.js" */
/* =require "/dragdrop/dragdrop-min.js" */

/* =require from "%WEB%" */
/* =require "/js/lib/prototype.min.js" */
/* =require "/js/ui/uibase.js" */
/* =require "/js/ui/uiFlashcardReview.js" */

