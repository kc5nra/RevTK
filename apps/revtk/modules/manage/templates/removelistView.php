<?php use_helper('Form', 'Widgets') ?>

<div id="manage-cards" class="layout-rindex">
  <div class="col-main col-box">

    <div class="app-header">
      <h2><a href="Home">Home</a> <span>&raquo;</span> Manage flashcards</h2>
      <div class="clear"></div>
    </div>

    <div class="uiSideTabs">
      
      <?php include_partial('SideNav', array('active' => 'removelist')) ?>
      
      <div class="views">
        <div id="manage-view">
          
          <h3>Remove Flashcards From List</h3>

          <div class="ajax">
            <?php include_partial('RemoveList') ?>
          </div>

        </div>
      </div>
      <div class="clear"></div>
    </div>

  </div>

</div>
