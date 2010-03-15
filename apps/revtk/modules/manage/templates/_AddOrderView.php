<?php use_helper('Form', 'Validation', 'Widgets') ?>

<?php echo form_tag('manage/addOrderConfirm', array('class' => 'main-form')) ?>

  <?php echo form_errors() ?>
  
  <p> You can easily add flashcards for RTK in two ways:</p>
  <ul class="content">
    <li>Enter a RTK index number, and all cards up to that number
        will be added</li>
    <li>Enter a range of cards, by using a "+" prefix, for example "+10"
        adds 10 flashcards.</li>
  </ul>
  
  <p>  <?php echo input_tag('txtSelection', '', array('class' => 'textfield', 'style' => 'width:80px')) ?>&nbsp;&nbsp;<?php echo submit_tag('Add Cards') ?></p>

  <div style="background:#E8E5C9;color:#000;padding:5px 10px; font-size:11px;">
    Note: adding RTK flashcards in order will  always fill in the missing cards,
    so don't use this if you have or plan to remove cards in the RTK range!
    Once you start removing cards in the RTK range you should use the "Custom
    selection" instead, to add just the cards you need.
  </div>

</form>
