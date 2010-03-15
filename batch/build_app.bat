@ECHO off
REM ************************************************************
REM Batch actions for website deployment:
REM 
REM - Minify specified Javascripts
REM - Compile the resource version include file used by framework
REM   and mod_rewrite to direct clients always to the up-to-date
REM   version of the Javascript and CSS files + caching.
REM
REM ************************************************************
REM
REM Example using YUICompressor
REM java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar web/js/1.0/autocomplete.js -o web/js/1.0/autocomplete.min.js
REM

SETLOCAL EnableDelayedExpansion

SET web_dir=%CD%\web\
SET yui_cmd=java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar
SET php_cmd=php.exe

REM EXIT /B

ECHO Minifying Javascripts with YuiCompressor...
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
REM %yui_cmd% %web_dir%/js/ui/uiform.js         -o %web_dir%/js/ui/uiform.min.js
REM %yui_cmd% %web_dir%/js/ui/uiview.js         -o %web_dir%/js/ui/uiview.min.js


ECHO Updating Versioning File (/config/versioning.inc.php) ...
ECHO ""

MOVE /Y .\config\versioning.inc.php .\config\_versioning.inc.php.last
%php_cmd% batch/build_app.php --webroot web --out config/versioning.inc.php

ECHO DONE!

PAUSE
