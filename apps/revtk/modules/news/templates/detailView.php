<?php slot('title', 'test') ?>

<div class="layout-news">

  <div class="col-main col-box col-box-top">

  <div class="app-header">
    <h2><?php echo $title ?>&nbsp;&nbsp;&nbsp; <span class="news-date"><?php echo $post->date ?></span></h2>
    
    <div class="clearboth"></div>
  </div>

  <?php if ($post): ?>
  <!--?php include_partial('news/list', array('newsPosts' => $newsPosts)) ?-->
  <div id="sitenews">
    <dl>
    <dd><?php echo $post->text ?></dd>
    </dl>
  </div>
  <?php endif ?>

  </div>

  <?php include_partial('archiveList') ?>

  <?php include_partial('home/homeSide', array()) ?>

</div>
