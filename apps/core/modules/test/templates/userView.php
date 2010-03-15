<h2>User Session Tests</h2>

<p>Edit the view file <?php echo __FILE__ ?> to run some tests on coreUser.

<p> Contents of the coreUser attributes:

<?php pre_start('printr') ?>
<?php print_r($_user->getAttributeHolder()->getAll()) ?>
<?php pre_end() ?>

<p> Contents of the $_SESSION array:

<?php pre_start('printr') ?>
<?php print_r($_SESSION) ?>
<?php pre_end() ?>

