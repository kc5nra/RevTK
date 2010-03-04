<h2>Security demo : LOGIN</h2>

<p> This is the default <b>login</b> page as configured in settings.php.


<?php use_helper('Form') ?>

<form method="post" action="<?php echo url_for('test/securitydemo') ?>">
<p> Sign in as: <?php echo content_tag('button', 'admin', array('name' => 'btnSigninAdmin')) ?>&nbsp;
    <?php echo content_tag('button', 'member', array('name' => 'btnSigninMember')) ?>
</form>
