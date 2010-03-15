<?php use_helper('Widgets', 'CJK', 'Date', 'Links') ?>

<div class="layout-default">
  <div class="col-main" style="width:840px;">
    <div class="col-box col-box-top fullkanjilist">


  <div class="app-header">
    <h2><?php echo link_to('Review','@overview') ?> <span>&raquo;</span> Detailed flashcard list</h2>

    <div class="clearboth"></div>
  </div>
  
  <div class="intro">
    <p>
      This list shows all your flashcards. Click a column heading to sort
      the table on that column, click more than once to revert the sort order.
      Note that in addition to the column you selected, there is always a secondary
      sorting on the frame number. Click in any row to go to the study area.
    </p>
  </div>
  <div class="stats">
    <div class="box">

      <strong>Statistics</strong><br />
      <?php echo ReviewsPeer::getFlashcardCount($_user->getUserId()) ?> flashcards.<br />
    </div>
  </div>
  <div class="clear"></div>
<?php #DBG::user() ?>
<?php echo ui_select_table($table, $pager) ?>

  </div>
  </div>
</div>