<div class="layout-study">

  <?php include_partial('SideColumn', array('framenum' => $framenum)) ?>

  <div class="col-main col-box col-box-top">

<?php if (!$framenum): ?>
  
  <div class="app-header">
    <h2>Search : No results</h2>
    <div class="clearboth"></div>
  </div>
  
  <p> Sorry, there are no results for "<strong><?php echo escape_once($_params->get('search')) ?></strong>".</p>

<?php else: ?>

  <div id="EditStoryComponent">
    <div class="app-header">
      <h2>Lesson <?php echo $kanjiData->lessonnum ?></h2>
      <div class="clearboth"></div>
    </div>
    <?php include_component('study', 'EditStory', array('framenum' => $framenum, 'kanjiData' => $kanjiData, 'reviewMode' => false)) ?>
  </div>
  
  </div>
  
  <div id="SharedStoriesComponent" class="col-main col-box">
  <?php include_component('study', 'SharedStories', array('framenum' => $framenum, 'kanjiData' => $kanjiData)) ?>
  </div>

<?php endif ?>

</div>
