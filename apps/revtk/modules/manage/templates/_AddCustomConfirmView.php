<?php use_helper('Form', 'Validation', 'Widgets') ?>

<?php echo form_tag('manage/addCustomProcess', array('class' => 'main-form')) ?>

  <?php if ($countNewCards==0 && $countExistCards): ?>

  <p> All kanji in the selection are already present in your flashcards.</p>

  <?php else: ?>

  <p> <strong><?php echo $countNewCards ?></strong> new card(s) will be added<?php if ($countExistCards) { echo sprintf(
      ' (%d are already in your flashcards)', $countExistCards); } ?>:</p>  

  <div style="background:#F3F1DC;color:#000;padding:5px;margin:0 0 1em;">
<?php
  $cards = array();
  foreach ($newCards as $id)
  {
    $cards[] = rtkBook::getKanjiForIndex($id);
  }
  echo implode(', ', $cards);
?>
  </div>

  <?php endif ?>

  <p>
    <?php if ($countNewCards > 0) { echo submit_tag('Add Cards') . '&nbsp;&nbsp;'; } ?><a href="#" class="cancel" onclick="return ManageFlashcards.load(this,{'cancel':true});">Go back</a>
  </p>


</form>

