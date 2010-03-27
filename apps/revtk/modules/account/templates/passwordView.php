<div class="layout-home">
  
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  <div class="col-box col-box-top block">

    <h2>Change Password</h2>

    <?php echo form_errors() ?>

    <?php echo form_tag('account/password', array('class'=>'block')) ?>
  
    <ul>
    <li><span class="lbl"><?php echo label_for('oldpassword', 'Old Password') ?><span class="req">*</span></span>
      <span class="fld medium"><?php echo input_password_tag('oldpassword', '', array('class' => 'textfield')) ?></span>
    </li>
    <li><span class="lbl"><?php echo label_for('newpassword', 'New Password') ?><span class="req">*</span></span>
      <span class="fld medium"><?php echo input_password_tag('newpassword', '', array('class' => 'textfield')) ?></span>
    </li>
    <li><span class="lbl"><?php echo label_for('newpassword2', 'Confirm New Pass') ?><span class="req">*</span></span>
      <span class="fld medium"><?php echo input_password_tag('newpassword2', '', array('class' => 'textfield')) ?></span>
    </li>
    <li><span class="lbl"></span>
      <span class="btn"><?php echo submit_tag('Change Password') ?></span>
    </li>
    </ul>
    </form>

<?php if (coreConfig::get('app_forum_url')): ?>
    <p class="small">
      <span class="required-legend">*</span> Note: this will also update your password on the <?php echo link_to('RevTK community forums', coreConfig::get('app_forum_url')) ?>.
    </p>
<?php endif ?>

  </div>
  </div>
 
</div>

<script type="text/javascript">
dom.addEvent(window,'load',function()
{
  $('oldpassword').focus();
});
</script>
