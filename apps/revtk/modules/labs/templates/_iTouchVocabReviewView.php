<?php use_helper('Form', 'Widgets', 'Gadgets') ?>


<?php # Ajax loading indicator ?>
<div id="uiFcAjaxLoading" style="display:none"><span class="l"></span><span>Loading<span>&nbsp;</span></span><span class="r"></span></div>

<?php # Connection timeout message ?>
<div id="uiFcAjaxError" class="uiFcErrorMsg" style="display:none"><div class="l"></div><div><span class="msg">Oops!</span>&nbsp;&nbsp;&nbsp;<a href="#" class="uiFcAction-reconnect">Reconnect</a></div><div class="r"></div></div>


<div class="uiFcOptions">
  <a href="#" style="display:none" class="uiFcOptBtn uiFcOptBtnUndo uiFcAction-back" title="Go back one flashcard"><span><u>B</u>ack</span></a>
</div>


<table class="uiFcLayout uiFcReview" cellspacing="0">
<tr class="top">
  <td colspan="3" class="layout">
  </td>
</tr>
<tr class="middle">
  <td width="10%" class="layout">&nbsp;</td>
  <td width="33%" class="layout">

    <?php # flashcard container ?>
    <div class="uiFcCard" style="diplay:none">

      <div class="fcData fcData-dispword fcKanjiFont" lang="ja" xml:lang="ja">&nbsp;</div>
      <div class="fcData fcData-reading fcKanjiFont" lang="ja" xml:lang="ja">&nbsp;</div>
      <div class="fcData fcData-glossary">&nbsp;</div>
      
      <div class="uiFcLoading">
        <p>Loading&nbsp;&nbsp;<img src="/images/1.0/ico/ajax_activity.gif" width=16 height=16 border=0 alt="" /></p>
      </div>

    </div>

  </td>
  <td width="33%" class="layout">
    
    <?php # Stats panel (displays when first card is loaded) ?>
    <div class="uiFcStats" style="display:none">

      <?php ui_box_rounded('uiFcBoxBlueR') ?>
        <h3>Card: <em class="count">.</em> of <em class="count">.</em></h3>
        <?php echo ui_progress_bar(array(array('value' => 0)), 100, array('id' => 'review-progress', 'borderColor' => '#5FA2D0')) ?>
      <?php echo end_ui_box() ?>

      <div class="wide-button">
        <?php ui_box_rounded('uiFcBoxBlueR') ?>
          <?php echo link_to('Go back', 'labs/index'); ?>
        <?php echo end_ui_box() ?>
      </div>
      <div class="wide-button">
        <?php ui_box_rounded('uiFcBoxBlueR') ?>
          <?php echo link_to('Search on google.co.jp', '', array('id' => 'search-google-jp', 'title' => 'Search this word on Google Japan')); ?>
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
        <h3>Press Spacebar to continue</h3>
        <p>
          <?php echo ui_ibtn('Flip Card', '', array('class' => 'uiFcAction-flip')) ?>
        </p>
      </div>
  
      <div id="uiFcButtons1" style="display:none">
        <h3>Press Spacebar to continue</h3>
        <p>
          <?php echo ui_ibtn('Next Card', '', array('class' => 'uiFcAction-flip')) ?>
        </p>
      </div>
      
    </div>
    
  </td>
  <td class="layout"></td>
</tr>
</table>

<script type="text/javascript">
var options =
{
  // the page to go to when clicking End with 0 reviews
  back_url:    "<?php echo url_for('labs/index', true) ?>",
  
  fcr_options: {
    max_undo:    10,
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

