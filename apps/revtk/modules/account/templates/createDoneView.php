<div class="layout-home">
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  <div class="col-box col-box-top block">

    <h2>Welcome</h2>
    
    <p>  Welcome <strong><?php echo $username ?></strong>!</p>
    
    <p> You have been succesfully registered on Reviewing the Kanji.</p>

    <p>  You can now <?php echo link_to('log in','@login', array('query_string' => 'username='.$username)) ?> with your username and password.<br/><br/></p>
    
    <h2>Forum Registration</h2>

    <?php echo form_errors() ?>

    <p>  As a member of Reviewing the Kanji, you have been registered also on the
      <a href="<?php echo coreConfig::get('app_forum_url') ?>" target="_blank">RevTK Community forums</a>, with the <strong>same username and password</strong>.
    </p>

    <p> For your convenience, when you login on the main site, <strong>you will also be logged in
      into the forums</strong>. If you update your password on the main site, your forum password
      is also updated. If you sign off from the main site, you are also signed off from the forums. 
    </p>    
    <p>  In the forums you will be able to discuss with other members,
      exchange tips and advice for completing  "Remembering the Kanji",
      and just generally chat away about all things Japanese and Japanese learning.
      Please read the <a href="<?php echo coreConfig::get('app_forum_url').'/misc.php?action=rules' ?>" target="_blank">forum rules</a>.
    </p>

  </div>
  </div>
 
</div>
