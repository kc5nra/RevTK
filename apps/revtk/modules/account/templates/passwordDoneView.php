<div class="layout-home">
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  <div class="col-box col-box-top block">

    <h2>Password Change Confirmation</h2>
    
    <p> Your password was succesfully updated on this site and on your forum account.
    </p>

    <?php echo form_errors() ?>

    <p> You are now <b>logged out</b>. 
    </p>
    <p> Please <?php 
      echo link_to('log in with your new password.','@login', array('query_string' => 'username='.$username)) ?> 
    </p>

  </div>
  </div>
 
</div>
