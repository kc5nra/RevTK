<?php use_helper('Form', 'Validation') ?>

<?php slot('inline_styles') ?>
<?php end_slot() ?>

<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
    <div class="col-box col-box-top content">

      <div class="app-header">
        <h2>Kanji Labs</h2>
        <div class="clearboth"></div>
      </div>
      
      <p> Welcome to the Reviewing the Kanji <strong>Labs</strong>!</p>

<?php if (CORE_ENVIRONMENT !== 'prod'): ?>
  <div style="-moz-border-radius:10px;padding:10px;background:#0077d4;color:#fff;font-weight:bold;margin:0 0 1em;border-radius:10px;">
    Please note this feature is currently on the TEST website! Sorry for the confusion,
    make sure to go back to <a href="http://kanji.koohii.com" style="color:#fc4">http://kanji.koohii.com</a> to use
    the <em>real</em> website. This is a development feature.
  </div>
<?php endif; ?>

      <p> This part of the website contains experimental features.</p>

      <p> Here I will "brainstorm" some ideas and with your feedback
          I hope eventually some of these features will become permanent
          on the website.</p>
      
      <h3>iVocab Shuffle™</h3>

      <p> This ultra simplistic flashcard review mode is inspired by
          Apple's iPod Shuffle. Press Start and then just press SPACE
          to flip cards, indefinitely.
      </p>

      <p> This is meant to be an informal test which can expose you to new
          words, and gradually get a better idea of the meaning of the
          characters.
      </p>
      
      <p>
        <?php echo link_to('<span>Start iVocab Shuffle™!</span>', 'labs/review', array('class' => 'uiIBtn uiIBtnDefault')) ?>
      </p>

      <p> Features:</p>
      <ul>
        <li><strong>Kanji Keywords</strong>: hover on the kanji with the mouse pointer to see a
        tooltip with the <em>Remembering the Kanji</em> keyword.</li>
        <li><strong>Study links</strong>: click any kanji to go to the corresponding
        Study page (hint: use Shift-click or Middle mouse button to open a new tab).</li>
        <li><strong>Discover new words</strong>: each new test will display a random
            selection of the "priority" entries defined in Jim Breen's
            Japanese/English dictionary (JMDICT).</li>
      </ul>

    </div>
  </div>

</div>
