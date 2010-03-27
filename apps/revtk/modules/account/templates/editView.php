<div class="layout-home">
  
<?php use_helper('Form', 'Validation') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  <div class="col-box col-box-top block">

    <div class="app-header">
      <h2>My Profile <span>&raquo;</span> Edit Account</h2>
      <div class="clear"></div>
    </div>

    <?php echo form_errors() ?>
    
    <?php echo form_tag('account/edit', array('class'=>'block')) ?>

    <ul>

    <li><span class="lbl"><label for="username">Username</label><span class="req">*</span></span>
      <span class="fld medium"><span class="no-edit"><?php echo $_user->getUserName() ?></span></span>
    </li>
    <li><span class="lbl"><label for="email">Email</label><span class="req">*</span></span>
      <span class="fld long"><?php echo input_tag('email', '', array('class' => 'textfield')) ?></span>
    </li>
    <li><span class="lbl"><label for="location">Location</label></span>
      <span class="fld medium"><?php echo input_tag('location', '', array('class' => 'textfield')) ?>
      <span class="legend">Ex. "Japan" or "Tokyo Japan"</span>
      </span>
    </li>
    <li><span class="lbl"><label for="form[timezone]">Timezone</label></span>
      <span class="fld medium">
        <?php echo select_tag('timezone', options_for_select(rtkTimezones::$timezones, $_request->getParameter('timezone'))) ?>
      </span>
    </li>
    <li><span class="lbl"></span>
      <span class="btn"><?php echo submit_tag('Save Changes') ?></span>
    </li>
    </ul>

    </form>

    <p class="small">
      <span class="required-legend">*</span><strong>Email</strong> : please enter a valid e-mail address. Without it, you will
      not be able to retrieve your password should you forget it!<br />
    </p>


  </div>
  </div>
 
</div>
