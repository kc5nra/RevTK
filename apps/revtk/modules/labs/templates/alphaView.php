<?php use_helper('Form', 'Validation') ?>

<?php slot('inline_styles') ?>
<?php end_slot() ?>

<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
    <div class="col-box col-box-top">

    <div class="app-header">
      <h2>Kanji Labs</h2>
      <div class="clearboth"></div>
    </div>
    
    <p> Type in kanji.</p>

    <?php echo form_errors() ?>

    <?php echo form_tag('labs/alpha') ?>
      <?php echo textarea_tag('kanji', '', 'rows=6 cols=78') ?>
      <div class="buttons">
        <?php echo submit_tag('Show') ?>
        <?php echo tag('input', array('type'=>'button', 'value'=>'Clear', 'onclick'=>'clearit()')) ?>
      </div>
    </form>

    <?php
DBG::request();
    ?>

    </div>
  </div>

</div>
