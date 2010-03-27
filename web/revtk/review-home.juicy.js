/**
 * Review Dashboard
 * 
 * Juicer build:
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/revtk/review-home.juicy.js
 * 
 * Minification:
 *  java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar web/revtk/review-home.juiced.js -o web/revtk/review-home.min.js
 *   
 * @package RevTK
 * @author  Fabrice Denis
 */

/* =require from "%FRONT%" */
/* =require "/lib/prototype.min.js" */
/* =require "/lib/raphael.min.js" */
/* =require "/revtk/rkLeitnerView.js" */

/* =require from "%WEB%" */
/* =require "/js/ui/uibase.js" */
/* =require "/js/ui/widgets.js" */

