<?php use_helper('Widgets', 'Gadgets', 'CJK', 'Links') ?>
<?php $sRtKForumUrl = coreConfig::get('app_forum_url').'/viewforum.php?id=1' ?>

<?php #DBG::request() ?>

<div class="layout-reviewsummary">
  <div class="col-main col-box col-box-top">

	<div class="app-header">
		<h2><?php echo link_to('Review','@overview') ?> &raquo; Summary</h2>
		<div class="clearboth"></div>
	</div>
	
	<div class="brief">
		<h3><?php echo $title ?></h3>

		<p>Below is the list of flashcards for your last review session. Click the column titles to sort on frame number, keyword, etc.</p>

		<p>See the <?php echo link_to('detailed flashcard list','review/flashcardlist') ?> for a complete list of all your flashcards and past results.</p>

		<?php if ($numRemembered <= $numForgotten): ?>
		<p>Having trouble remembering the characters? Don't hesitate to browse the <?php echo link_to('RtK Volume 1 forum', $sRtKForumUrl) ?> where you can find help and guidance from fellow members!</p>
		<?php endif ?>

		&laquo; <?php echo link_to('Back to Review','@overview') ?>
	</div>
	
	<div class="chart">
		<?php ui_box_rounded('uiBoxRDefault') ?>
			<?php echo ui_chart_vs(array(
				'valueLeft' => $numRemembered,
				'labelLeft' => 'Remembered',
				'valueRight' => $numForgotten,
				'labelRight' => 'Forgotten'
			)) ?>
		<?php echo end_ui_box() ?>
	</div>
	
	<div class="clear"></div>

	<div id="summaryTable">
		<?php include_component('review', 'summaryTable', array('ts_start' => $ts_start)) ?>
	</div>

  </div>
</div>

<script type="text/javascript">
var SummaryPage = (function()
{
	return {
		initialize:function()
		{
			this.ajaxTable = new uiWidgets.AjaxTable($('summaryTable'));
		}
	}
})();
Event.observe(window, 'load', function(){ SummaryPage.initialize() } );
</script>
