<h2>Authentication & Credentials Demo</h2>

<p> Hello <samp><?php echo $_user->getAttribute('name') ?></samp>!

<ul>
<li> You are authenticated: <samp><?php echo $_user->isAuthenticated() ? 'Yes' : 'No' ?></samp>.

<li> Your credentials: <samp><?php
	$credentials = $_user->listCredentials();
	echo empty($credentials) ? 'None' : implode(', ', $credentials) ?></samp>.

<li> Credentials required by this action: <samp><?php 
	$credentials = coreContext::getInstance()->getActionInstance()->getCredential();
	echo is_null($credentials) ? 'None' : implode(', ', $credentials) ?></samp>.
</ul>

<p> Go to <?php echo link_to('ADMIN page', 'test/securityadmin') ?> (requires 'admin' credential).

<?php if (!$_user->isAuthenticated()): ?>
<p> Go to <?php echo link_to('LOGIN page', 'test/securitylogin') ?>
<?php endif; ?>

<?php use_helper('Form') ?>

<form method="post">
  <?php if ($_user->hasCredential('member') && !$_user->hasCredential('admin')): ?>
    <?php echo submit_tag('Become admin', array('name' => 'btnSigninAdmin')) ?>&nbsp;&nbsp;&nbsp;
  <?php endif; ?>

  <?php if ($_user->hasCredential('admin')): ?>
    <?php echo submit_tag('Become member', array('name' => 'btnSigninMember')) ?>&nbsp;&nbsp;&nbsp;
  <?php endif; ?>

  <?php if ($_user->isAuthenticated()): ?>
    <?php echo submit_tag('Sign out', array('name' => 'btnSignout')) ?>
  <?php endif; ?>
</form>

<h2>Request Parameters</h2>

<?php pre_start('printr'); print_r($_request->getParameterHolder()->getAll()); pre_end(); ?>


<h2>$_SESSION</h2>

<p> Note the $_SESSION array is updated with the user attributes on <b>shutdown</b> of the 
    user class, so if the action sets a user attribute, it will be displayed below only
	after another page refresh.

<?php pre_start('printr') ?>
<?php print_r($_SESSION) ?>
<?php pre_end() ?>
