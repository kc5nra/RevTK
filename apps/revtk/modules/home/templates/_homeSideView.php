<div class="col-side">
	<div style="margin-top:20px">
		<?php echo image_tag('/images/1.0/nav/home_kanji.gif', 'size="140x142"') ?>
	</div>
	<div id="homelinks">
		<?php echo link_to('Support the website', 'about/support', array('id'=>'donatelink')) ?>
		<?php echo link_to('News Archive', 'news/index') ?>
		<?php echo link_to('Contact', 'home/contact') ?>
		<?php echo link_to('About', 'about/index') ?>
	</div>

<?php use_helper('LocalAssets'); echo get_local_content(__FILE__, 'partners'); ?>

</div>
