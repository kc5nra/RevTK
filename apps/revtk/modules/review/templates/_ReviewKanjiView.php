<?php use_helper('Form', 'Widgets', 'Gadgets') ?>
<?php use_stylesheet('/css/2.0/widgets.css') ?>

<?php use_javascript('/js/ui/uibase.min.js') ?>
<?php use_javascript('/js/ui/uiFlashcardReview.min.js') ?>
<?php use_javascript('/js/2.0/review/review.js') ?>
<?php use_stylesheet('/css/2.0/review-flashcards-kanji.css') ?>

<?php # EditStory Window ?>
<?php use_javascript('/js/ui/widgets.js') ?>
<?php use_javascript('/js/2.0/review/rkEditStoryWindow.js') ?>
<?php use_javascript('/js/2.0/study/EditStoryComponent.js') ?>

<!-- Dependencies -->  
<?php use_javascript('/js/lib/yui/2.7.0/yahoo-dom-event.js') ?>
<?php use_javascript('/js/lib/yui/2.7.0/dragdrop-min.js') ?>

<?php 
	# Ajax EditStory window
	echo ui_window('&nbsp;', array('id' => 'EditStoryWindow'))
?>

<?php # Ajax loading indicator ?>
<div id="uiFcAjaxLoading" style="display:none"><span class="l"></span><span>Loading<span>&nbsp;</span></span><span class="r"></span></div>

<?php # Connection timeout message ?>
<div id="uiFcAjaxError" class="uiFcErrorMsg" style="display:none"><div class="l"></div><div><span class="msg">Oops!</span>&nbsp;&nbsp;&nbsp;<a href="#" class="uiFcAction-reconnect">Reconnect</a></div><div class="r"></div></div>


<table class="uiFcLayout uiFcReview" cellspacing="0">
<tr class="top">
	<td colspan="3" class="layout">
		<div class="uiFcOptions">
			<a href="#" class="uiFcOptBtn uiFcOptBtnStory uiFcAction-story" title="View/Edit story for this flashcard"><span><u>S</u>tory</span></a>
      <a href="#" style="display:none" class="uiFcOptBtn uiFcOptBtnUndo uiFcAction-undo" title="Go back one flashcard"><span><u>U</u>ndo</span></a>
		</div>
	</td>
</tr>
<tr class="middle">
	<td width="33%" class="layout">&nbsp;</td>
	<td width="33%" class="layout">

		<?php # flashcard container ?>
		<div class="uiFcCard" style="display:none">

			<div id="keyword" class="fcData fcData-keyword">&nbsp;</div>
			<div id="kanjibig" lang="ja" xml:lang="ja">
				<p><img src="/images/1.0/spacer.gif" width="1" height="1" alt=""/><span class="fcData fcData-kanji">&nbsp;</span></p>
			</div>
			<div id="strokecount"><span class="kanji" title="Stroke count" lang="ja" xml:lang="ja">&#30011;&#25968;</span> <span class="fcData fcData-strokecount">&nbsp;</span></div>
			<div id="framenum" class="fcData fcData-framenum">&nbsp;</div>
			
			<div class="uiFcLoading">
				<p>Loading&nbsp;&nbsp;<img src="/images/1.0/ico/ajax_activity.gif" width=16 height=16 border=0 alt="" /></p>
			</div>

		</div>

	</td>
	<td width="33%" class="layout">
		
		<?php # Stats panel (displays when first card is loaded) ?>
		<div class="uiFcStats" style="display:none">

			<?php ui_box_rounded('uiFcBoxBlueR') ?>
				<h3>Reviewed: <em class="count">.</em> of <em class="count">.</em></h3>
			  <?php echo ui_progress_bar(array(array('value' => 0)), 100, array('id' => 'review-progress', 'borderColor' => '#5FA2D0')) ?>
			<?php echo end_ui_box() ?>

			<div class="stacks">
				<?php ui_box_rounded('uiFcBoxBlueR') ?>
					<p class="pass" title="Cards remembered">0</p>
					<p class="fail" title="Cards forgotten">0</p>
				<?php echo end_ui_box() ?>
			</div>
			
			<div class="finish">
				<?php ui_box_rounded('uiFcBoxBlueR') ?>
					<a href="#" class="uiFcAction-end" title="Finish - go to review summary">End</a>
				<?php echo end_ui_box() ?>
			</div>

		</div><!-- uiFcStats -->
		
	</td>
</tr>
<tr class="bottom">
	<td class="layout"></td>
	<td class="layout">

		<?php # flashcard anwser buttons ?>
		<div class="uiFcButtons" id="uiFcButtons">
			
			<div id="uiFcButtons0" style="display:none">
				<h3>Press Spacebar or F to flip card</h3>
				<p>
					<?php echo ui_ibtn('<u>F</u>lip Card', '', array('class' => 'uiFcAction-flip')) ?>
				</p>
			</div>
	
			<div id="uiFcButtons1" style="display:none">
				<h3>Do you remember this kanji ?</h3>
				<?php 
					echo ui_ibtn('<u>N</u>o', '', array('class' => 'uiFcAction-no', 'title' => 'Forgotten')) .
					     ui_ibtn('<u>Y</u>es', '', array('class' => 'uiFcAction-yes', 'title' => 'Remembered with some effort')) .
							 ui_ibtn('<u>E</u>asy', '', array('class' => 'uiFcAction-easy', 'title' => 'Remembered easily'));
				?>
			</div>
			
		</div>
		
	</td>
	<td class="layout"></td>
</tr>
</table>

</div>

<?php # Form to redirect to Review Summary with POST ?>
<form method="post" id="uiFcRedirectForm" action="<?php echo url_for('@review_summary') ?>" style="display:none">
	<?php # Custom data to pass to the Review Summary (review.js onEndReview()) ?>
	<?php echo input_hidden_tag('ts_start', $ts_start) ?>
	<input type="hidden" name="fc_pass" value="0" />
	<input type="hidden" name="fc_fail" value="0" />
</form>

	
<script type="text/javascript">
var options = {
	end_url: "<?php echo url_for('@review_summary', true) ?>",
	editstory_url: "<?php echo url_for('study/editstory') ?>",

	fcr_options: {
  // num_prefetch: 10,
    ajax_url:  "<?php echo $ajax_url ?>",
    back_url:  "<?php echo url_for('@overview', true) ?>",
    items:     [<?php echo implode(',', $items) ?>]
	}
};

Event.observe(window, 'load', function() { rkKanjiReview.initialize(options); });
</script>
