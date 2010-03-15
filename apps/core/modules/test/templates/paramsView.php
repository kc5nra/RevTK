<h2>Request Parameters from Default Url Route</h2>

<p> This shows request parameters set for a URL like <samp>/test/params/var1/value1/var2/value2/...</samp>:

<p> Try <a href="/demo.php/test/params/kanji/女/eat/bananas">example link</a>.

<?php pre_start('printr'); print_r($_request->getParameterHolder()->getAll()); pre_end(); ?>

<?php
/*
  $_response->addHttpMeta('accept-language', 'en');
  $_response->addHttpMeta('accept-language', 'fr', false);
  DBG::out( $_response->getHttpHeader('accept-language') );
*/
?>
