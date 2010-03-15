<?php use_helper('Form', 'Url') ?>

<?php echo form_tag('test/urlencoding', array('class'=>'block')) ?>

<?php echo input_tag('variable', '"""', array('class' => 'textfield')) ?>&nbsp;&nbsp;<?php echo submit_tag('Test GET Variable Encoding') ?>

</form>

<p> <?php $vars = array('variable' => '&amp;'); 
  echo link_to('test', 'test/urlEncoding', array('query_string' => http_build_query($vars))) ?>

<p> <?php echo coreContext::getInstance()->getController()->genUrl(array(
  'module' => 'test', 'action'=>'urlEncoding', 'item'=>'"""')) ?>

<h2>$_GET</h2>

<?php DBG::printr($_GET) ?>

<h2>$_request</h2>

<?php DBG::printr($_request->getParameterHolder()->getAll()) ?>
