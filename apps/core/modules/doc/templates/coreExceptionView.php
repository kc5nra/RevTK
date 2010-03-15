<h2>coreException</h2>

<p> This exception will display a "500 Internal Server Error" page (in production environment).
<ul>
  <li>The default error page is at: <b>lib/core/exception/errors/error500.php</b>
  <li>If present, the custom error page will be displayed: <b>web/errors/error500.php</b>
</ul>

<?php pre_start() ?>
  throw new coreException('Division by zero.');
<?php pre_end() ?>

<h2>coreError404Exception</h2>

<p> This exception will display a 404 error page (in production environment).<br/>

<?php pre_start() ?>
throw new coreError404Exception();
<?php pre_end() ?>

<p> The <?php echo link_to('coreAction', 'doc/core?include_name=action') ?> class contains several methods to throw this exception: <b>forward404()</b>,
    <b>forward404If()</b> and <b>forward404Unless()</b>.

<p> You can customize the error 404 action and template in their default location: <samp>apps/<var>frontend</var>/modules/default/</samp>.
    Alternatively, you can set the <b>error_404_module</b> and <b>error_404_action</b> constants in the <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?>
  file to use an existing action.

<?php pre_start() ?>
// In settings.php
/**
 * Default Error 404 page.
 */
'error_404_module' => 'default',
'error_404_action' => 'error404',
<?php pre_end() ?>