@ECHO off
REM ************************************************************
REM Script to run before deployment of the production site.
REM 
REM - Build Juicer files (*.juicy.* pattern): concatenate
REM   css & javascript files, copies required assets to the
REM   web folder
REM - Minify css & javascript files with YUI Compressor
REM - Rebuild the versioning config file which tells the web
REM   response the version number for css & javascript files,
REM   which ensures clients always load the latest version.
REM 
REM ************************************************************
REM
REM NOTE!! The windows .bat file is no longer maintained, use the
REM 'build' script for reference.
REM

SETLOCAL EnableDelayedExpansion

SET web_dir=%CD%\web\
SET yui_cmd=java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar
SET php_cmd=php.exe

REM EXIT /B

ECHO Minifying with YuiCompressor...
ECHO ""

%yui_cmd% %web_dir%/js/1.0/autocomplete.js      -o %web_dir%/js/1.0/autocomplete.min.js
%yui_cmd% %web_dir%/js/1.0/reading.js           -o %web_dir%/js/1.0/reading.min.js
%yui_cmd% %web_dir%/js/1.0/help-system.js       -o %web_dir%/js/1.0/help-system.min.js

%yui_cmd% %web_dir%/js/2.0/review/review.js     -o %web_dir%/js/2.0/review/review.min.js
%yui_cmd% %web_dir%/js/2.0/study/study.js       -o %web_dir%/js/2.0/study/study.min.js
%yui_cmd% %web_dir%/js/2.0/study/EditStoryComponent.js   -o %web_dir%/js/2.0/study/EditStoryComponent.min.js

%yui_cmd% %web_dir%/js/lib/prototype.js         -o %web_dir%/js/lib/prototype.min.js
%yui_cmd% %web_dir%/js/lib/raphael.js           -o %web_dir%/js/lib/raphael.min.js

%yui_cmd% %web_dir%/js/ui/uibase.js             -o %web_dir%/js/ui/uibase.min.js
%yui_cmd% %web_dir%/js/ui/widgets.js            -o %web_dir%/js/ui/widgets.min.js
%yui_cmd% %web_dir%/js/ui/uiFlashcardReview.js  -o %web_dir%/js/ui/uiFlashcardReview.min.js

ECHO Building files with Juicer...
ECHO ""

REM php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/js/2.0/review.juicy.js
REM php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/js/2.0/study.juicy.js
php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/js/2.0/labs/alpha.juicy.js

ECHO Updating Versioning File (/config/versioning.inc.php) ...
ECHO ""

MOVE /Y .\config\versioning.inc.php .\config\.versioning.inc.php
%php_cmd% batch/build_app.php --webroot web --out config/versioning.inc.php

ECHO DONE!

PAUSE
