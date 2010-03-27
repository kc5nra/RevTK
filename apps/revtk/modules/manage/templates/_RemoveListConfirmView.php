<?php use_helper('Form', 'Validation', 'Widgets') ?>

<?php echo form_tag('manage/removeListProcess', array('class' => 'main-form')) ?>

  <?php if (!$count): ?>

  <p> No flashcards selected.</p>

  <?php else: ?>

  <p> <strong><?php echo $count ?></strong> card(s) will be deleted:</p>  

  <div style="background:#F3F1DC;color:#000;padding:5px;margin:0 0 1em;">
<?php
  $kanjis = array();
  foreach ($cards as $id)
  {
    $kanjis[] = rtkBook::getKanjiForIndex($id);
  }
  echo implode(', ', $kanjis);
?>
  </div>

  <?php endif ?>

  <p>
    <?php if ($count) { echo submit_tag('Delete Cards') . '&nbsp;&nbsp;'; } ?><a href="#" class="cancel" onclick="return ManageFlashcards.load(this,{'cancel':true});">Go back</a>
  </p>


</form>

