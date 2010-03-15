<?php use_helper('Form', 'Widgets') ?>

<div id="manage-cards" class="layout-rindex">
  <div class="col-main col-box">

    <div class="app-header">
      <h2><?php echo link_to('Home', '@homepage') ?> <span>&raquo;</span> Manage flashcards</h2>
      <div class="clear"></div>
    </div>

    <div class="uiSideTabs">
      
      <?php include_partial('SideNav', array('active' => 'addorder')) ?>
      
      <div class="views">
        <div id="manage-view">

          <h3>Add Remembering the Kanji flashcards</h3>

          <div class="ajax">
            <?php include_partial('AddOrder') ?>
          </div>

        </div>
      </div>
      <div class="clear"></div>
    </div>

  </div>

</div>
