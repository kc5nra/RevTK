<div class="layout-home">

<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
    <div class="col-box col-box-top">

    <div class="app-header">
      <h2>Message Sent</h2>
      <div class="clearboth"></div>
    </div>

    <div class="confirmwhatwasdone">
      <p>Your message has been sent succesfully.</p>
      <p> <strong>Thank you for your feedback!</strong></p>
    </div>

    <p> Please note that due to real life &trade;, I may not always reply to your email.
      If you asked a question I will try to answer shortly.<br/>
      All feedback is read and appreciated! - <em>Fabrice</em>
    </p>
    
    <p> &raquo; <?php echo link_to('Go to home page', '@homepage') ?>
    </p>

  </div>
  </div>

</div>
