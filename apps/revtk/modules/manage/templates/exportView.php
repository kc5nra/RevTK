<?php use_helper('Form', 'Widgets') ?>
<?php use_stylesheet('/css/2.0/widgets.css'); /*export button*/ ?>

<div id="manage-cards" class="layout-rindex">
  <div class="col-main col-box">

    <div class="app-header">
      <h2><a href="Home">Home</a> <span>&raquo;</span> Manage flashcards</h2>
      <div class="clear"></div>
    </div>

    <div class="uiSideTabs">
      
      <?php include_partial('SideNav', array('active' => 'exportflashcards')) ?>
      
      <div class="views">
        <div id="manage-view">
          
          <h3>Export your kanji flashcards</h3>
          
          <p>Click the link below to download your flashcards and current review status. The data
             is exported as a CSV file, using UTF-8 encoding.
             
<?php if (coreConfig::get('app_forum_url')): ?>
          <p>Learn more about using CSV data <?php echo link_to('here', coreConfig::get('app_forum_url').'/viewtopic.php?pid=81160')?>.</p>
<?php endif ?>

          <p><?php echo ui_ibtn('Export flashcards', 'manage/exportflashcards', array('icon' => 'export')) ?></p>
          

        </div>
      </div>
      <div class="clear"></div>
    </div>

  </div>

</div>
