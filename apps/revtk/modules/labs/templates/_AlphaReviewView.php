<?php use_helper('Form', 'Widgets', 'Gadgets') ?>
<?php use_stylesheet('/css/2.0/widgets.css') ?>

<?php use_javascript('/js/ui/uibase.js') ?>
<?php use_javascript('/js/ui/uiFlashcardReview.js') ?>
<?php use_javascript('/js/2.0/labs/labsReview.js') ?>
<?php use_stylesheet('/js/2.0/labs/labsReview.css') ?>


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

      <div id="keyword" class="fcData fcData-compound">&nbsp;</div>
      <div style="font-size:2em;" class="fcData fcData-reading">&nbsp;</div>
      <div style="font-size:1.5em;" class="fcData fcData-glossary">&nbsp;</div>
      
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

<?php # Form to redirect to Review Summary with POST ?>
<?php echo form_tag('labs/summary', array('id' => 'uiFcRedirectForm', 'style' => 'display:none')) ?>
  <?php # Custom data to pass to the Review Summary ?>
  <!--?php echo input_hidden_tag('ts_start', $ts_start) ?-->
  <input type="hidden" name="fc_pass" value="0" />
  <input type="hidden" name="fc_fail" value="0" />
</form>

<script type="text/javascript">
var options =
{
  // the page to go to when clicking End with 0 reviews
  back_url:    "<?php echo url_for('labs/alpha', true) ?>",
  end_url:     "<?php echo url_for('labs/summary', true) ?>",
  
  fcr_options: {
    //num_prefetch: 10,
    ajax_url:    "<?php echo $_context->getController()->genUrl('labs/review') ?>",
    put_request: false,
    items:       [<?php echo implode(',', $items) ?>]
  }
};

uiConsole.enabled = <?php echo (CORE_ENVIRONMENT === 'dev') ? 'true' : 'false' ?>;

Event.observe(window, 'load', function()
{
  labsReview.initialize(options);
});
</script>

