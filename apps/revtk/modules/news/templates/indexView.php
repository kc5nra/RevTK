<div class="layout-news">

  <div class="col-main col-box col-box-top">

  <div class="app-header">
    <h2><?php echo $title ?></h2>
    <div class="clearboth"></div>
  </div>
  
  <?php if(!empty($newsPosts)): ?>
  <?php include_partial('news/list', array('newsPosts' => $newsPosts)) ?>
  <?php else: ?>
  <p>There are no news within this date range.</p>
  <?php endif ?>
  
  </div>

  <?php include_partial('archiveList') ?>

  <?php include_partial('home/homeSide', array()) ?>

</div>
