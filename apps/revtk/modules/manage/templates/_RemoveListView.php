<?php use_helper('Form', 'Validation', 'Widgets') ?>

<?php if (!ReviewsPeer::getFlashcardCount($_user->getUserId())): ?>

  <p> There aren't any flashcards to delete.</p>

<?php else: ?>

  <p> <span class="warning">Remove flashcards</span> by selecting items in the list below.</p>
  
  <?php echo form_errors() ?>
  
  <div class="selection-table">
    <?php include_component('manage', 'RemoveListTable') ?>
  </div>
  
  <?php echo form_tag('manage/removeListConfirm', array('class' => 'main-form')) ?>
    <p> <?php echo submit_tag('Remove Cards') ?>&nbsp;&nbsp;<?php echo link_to('Clear selection', 'manage/removelist', array('class' => 'cancel')) ?></p>
  </form>

<?php endif ?>