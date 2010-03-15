<?php use_helper('Form', 'Validation', 'Widgets') ?>

<?php echo form_tag('manage/addCustomConfirm', array('class' => 'main-form')) ?>

  <p> To add a custom selection of flashcards, enter one or more of the following:</p>
  <ul class="content">
    <li>Remembering the Kanji frame numbers (numbers 1 to 3007), eg: "1, 3, 5"</li>
    <li>A range of frame numbers, eg: "10-20" or "1-5, 10-15"</li>
    <li>Kanji characters, separators are not required,<br/>
        eg: "一, 二, 三" or "一二三"</li>
  </ul>
  <p> All numbers and number ranges must be separated with blanks or commas.</p>

  <?php echo form_errors() ?>
  
  <?php echo textarea_tag('txtSelection', '' /*'4 56 一　二三'*/, array('class' => 'text', 'cols' => 70, 'rows' => 5)) ?><br/>
  <?php echo submit_tag('Add Cards') ?>

</form>
