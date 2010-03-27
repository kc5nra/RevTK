<h2>coreUser</h2>

<p> Wraps the user session, allows to store and retrieve user attributes.</p>

<?php pre_start() ?>
// Retrieves the User session parameter holder
// @return sfParameterHolder
function getAttributeHolder()

// Proxy methods for accessing the parameter holder
function getAttribute($name, $default = null)
function hasAttribute($name)
function setAttribute($name, $value)
<?php pre_end() ?>

<h2>Accessing the User Session</h2>

<p> Actions and Components have a <b>getUser()</b> method:
<?php pre_start() ?>
$user = $this->getUser();
<?php pre_end() ?>

<p> In a view template, use the <b>$_user</b> variable, for example:

<?php pre_start() ?>
&lt;?php echo $_user->getAttribute('name', 'Anonymous Coward') ?>
<?php pre_end() ?>

<p> From anywhere else, the user session is always available from the Context object:

<?php pre_start() ?>
$user = coreContext::getInstance()->getUser();
<?php pre_end() ?>


<h2> Clearing User Attributes</h2>

<p> To clear user attributes, use the <b>remove()</b> method of the <?php echo link_to('sfParameterHolder', 'doc/lib?page_id=sfParameterHolder') ?> class:

<?php pre_start() ?>
$user->getAttributeHolder()->remove('login');
<?php pre_end() ?>


<p> To clear <em>all</em> user attributes, use the <b>clear()</b> method of the <?php echo link_to('sfParameterHolder', 'doc/lib?page_id=sfParameterHolder') ?> class:

<?php pre_start() ?>
$user->getAttributeHolder()->clear();
<?php pre_end() ?>

<!--?php
DBG::printr($_SESSION);
$_user->getAttributeHolder()->clear();
/*
$_user->setAttribute('joe', true);
$_user->setAttribute('moe', true);
$_user->setAttribute('toe', true);
*/
-->
