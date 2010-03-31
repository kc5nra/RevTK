<div class="col-side">
  <?php echo image_tag('/images/1.0/nav/home_kanji_2.gif', 'size="131x131" style="margin:0 0 0 5px;"') ?>
<?php /*  
  <div id="homelinks">
    <?php echo link_to('Support the website', 'about/support', array('id'=>'donatelink')) ?>
    <?php echo link_to('News Archive', 'news/index') ?>
    <?php echo link_to('Contact', 'home/contact') ?>
    <?php echo link_to('About', 'about/index') ?>
  </div>*/
?>
  <p style="margin:10px 0 10px;font:bold 18px/1.2em Georgia, serif;text-align:center;color:#858376;">Sponsors</p>
<?php use_helper('LocalAssets'); echo get_local_content(__FILE__, 'partners'); ?>

</div>
