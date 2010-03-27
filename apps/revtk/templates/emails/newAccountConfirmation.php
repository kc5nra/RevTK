Welcome <?php echo $username ?>!

You have registered at Reviewing the Kanji ( <?php echo coreConfig::get('app_website_url') ?> )
with the following information :

Username :  <?php echo $username."\n" ?>
Password :  <?php echo $password."\n" ?>
Email    :  <?php echo $email."\n" ?>

<?php if ($forum_uid): ?>
You have also been registered with the same username and password
on the Reviewing the Kanji community forums:
<?php echo coreConfig::get('app_forum_url')."\n" ?>

You can access your forum profile here:
<?php echo coreConfig::get('app_forum_url') ?>/profile.php?id=<?php echo $forum_uid."\n" ?>
<?php endif ?>

-Reviewing the Kanji Mailer.
