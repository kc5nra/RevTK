<?php use_helper('CJK', 'Form', 'Validation') ?>
<?php use_javascript('/js/2.0/study/EditStoryComponent.js') ?>

<?php echo form_tag('study/edit?id='.$framenum, array('name' => 'EditStory')) ?>

  <?php # state variables ?>
  <?php echo input_hidden_tag('framenum', $framenum) ?>
  <?php if ($reviewMode): ?>
    <input type="hidden" name="reviewMode" value="1" />
  <?php endif ?>

  <div id="my-story">
    <div class="padding rtkframe">

      <div class="left">
        <span class="framenum" title="Frame number"><?php echo $kanjiData->framenum ?></span>
        <div class="kanji"><?php echo cjk_lang_ja($kanjiData->kanji) ?></div>
        <span class="strokecount" title="Stroke count">[<?php echo $kanjiData->strokecount ?>]</span>
      </div>

      <div class="right">
        <div class="keyword"><?php echo KanjisPeer::getDisplayKeyword($kanjiData->keyword) ?></div>

        <?php echo form_errors() ?>

        <div id="storybox">

          <?php # Story Edit ?>
          <div id="storyedit" style="display:none;">

            <?php echo textarea_tag('txtStory', '', 'rows="12" cols="55" id="frmStory"') ?>

            <div class="controls valign">
              <div style="float:left;">
                <?php echo checkbox_tag('chkPublic') ?>
                <?php echo label_for('chkPublic', 'Share this story') ?>
              </div>
              <div style="float:right;">
                <?php echo submit_tag('Save changes', array('name' => 'doUpdate', 'title' => 'Save/Update story')) ?>
                <input type="button" id="storyedit_cancel" value="Cancel" name="cancel" title="Cancel changes" />
              </div>
              <div class="clear"></div>
            </div>
          </div>
          
          <?php # Story View ?>
          <div id="storyview">
            <div id="sv-textarea" class="bookstyle<?php echo empty($formatted_story) ? ' empty' : '' ?>" title="Click to edit your story" style="display:block;">
              <?php echo !empty($formatted_story) ? $formatted_story : '[ click here to enter your story ]' ?>
            </div>

<?php if (!$reviewMode && $isRestudyKanji): ?>
  <?php if (!$isRelearnedKanji): ?>
            <div class="controls">
              <?php echo input_tag('doLearned', '', array(
                'type' => 'image', 'src' => '/images/2.0/study/restudy-button.gif',
                'alt'  => 'Add to restudied list',
                'title'=> 'Add kanji that you have relearned to a list for review later' )) ?>
            </div>
  <?php else: ?>
            <div class="msg-relearned">This kanji is ready for review in the <strong>restudied</strong> list.</div>
  <?php endif ?>
<?php endif ?>

          </div>
<?php #DBG::printr($_params->getAll()) ?>
        </div>

      </div>

    </div>
    <div class="bottom"></div>
  </div>

</form>
