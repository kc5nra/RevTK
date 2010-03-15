<?php use_helper('Form') ?>
<?php use_javascript('/js/2.0/study/study.js') ?>

<?php # Constants for the javascripts ?>
<script type="text/javascript">
Event.observe(window, 'load', function()
{
  StudyPage.initialize({
    URL_SEARCH:        "<?php echo url_for('study/kanji', true) ?>",
    URL_SHAREDSTORIES: "<?php echo url_for('study/ajax', true) ?>"
  });  
});
</script>

<div class="study-left">

  <div class="col-box col-box-top">
    <div class="app-header">
      <h2><?php echo link_to('Study','study/index', 'title="Go to Study > Introduction"') ?> <span>&raquo;</span></h2>
      <div class="clearboth"></div>
    </div>
    <?php include_partial('study/StudySearch', array('framenum' => $framenum)) ?>
  </div>

  <div class="col-box col-tbar">
    <h2>My Stories</h2>
    <span class="q">&raquo;</span> <?php echo link_to('All my stories','study/mystories') ?>
  </div>
  
  <div class="col-box col-tbar study-restudy">
    <h2>Restudy Kanji</h2>

<?php if ($learnedCount = LearnedKanjiPeer::getCount($_user->getUserId())): ?>
    <p class="set">
      <em><?php echo $learnedCount ?></em> learned<br/>
      <span class="btn">
        <?php echo link_to('<img src="/images/2.0/study/review-small.gif" alt="Review" width="56" height="19" />', '@review', array('query_string' => 'type=relearned')) ?>&nbsp;&nbsp;
        <?php echo link_to('Clear', 'study/clear', array('class' => 'cancel')) ?>
      </span>
    </p>
<?php endif ?>

<?php if ($restudyCount = ReviewsPeer::getRestudyKanjiCount($_user->getUserId())): ?>
    <p class="set">
      <em><?php echo $restudyCount ?></em> to restudy<br/>
      <span class="btn">
        <?php echo link_to('<img src="/images/2.0/study/review-small.gif" alt="Review" width="56" height="19" />', '@review', array('query_string' => 'box=1')) ?>
      </span>
    </p>

    <div class="failed-kanji">
      
      <p>
        <span class="q">&raquo;</span> <?php echo link_to('Detailed List','study/failedlist') ?> <span class="count">(<?php echo $restudyCount ?>)</span>
      </p>
      <?php include_partial('RestudyList', array('framenum' => $framenum)) ?>
    </div>
<?php endif ?>
  </div>

</div>
