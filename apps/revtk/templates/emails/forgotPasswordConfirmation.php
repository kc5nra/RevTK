Dear <?php echo $username ?>,

A request for a new password was sent to this address.

Because we can not read your current password, a new random password
is generated for you, that allows you to log back into the site.

Please sign in (<?php echo coreConfig::get('app_website_url') ?>/login) with the following information:

Username :  <?php echo $username."\n" ?>
Password :  <?php echo $password."\n" ?>

After you signed in, you may want to go to your profile page
(<?php echo coreConfig::get('app_website_url') ?>/profile) and update the password
to something that you like.

-Reviewing the Kanji Mailer.

F.A.Q.

=> Did you use the same email address for multiple accounts?

   Please contact the webmaster through the Contact page.
