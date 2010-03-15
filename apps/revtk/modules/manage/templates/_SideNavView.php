<?php
slot('ManageSideNav', $active);
/**
 * Helper to set active class on the active list item.
 * 
 */
function manageSideNav($id, $text, $internal_uri)
{
  $options = get_slot('ManageSideNav') === $id ? array('class' => 'active') : array();
  return tag('li', $options, true) . link_to($text, $internal_uri) . '</li>';
}
?>
      <div class="ui-tabs" id="manage-cards-tabs">

        <h2>Add Cards</h2>
        <ul>
          <?php echo manageSideNav('addorder', 'Remembering the Kanji', 'manage/index') ?></li>
          <?php echo manageSideNav('addcustom', 'Custom selection', 'manage/addcustom') ?></li>
        </ul>
        <h2>Remove Cards</h2>
        <ul>
          <?php echo manageSideNav('removelist', 'Select from list', 'manage/removelist') ?></li>
          <?php echo manageSideNav('removecustom', 'Custom selection', 'manage/removecustom') ?></li>
        </ul>
        
        <h2>Export</h2>
        <ul>
          <?php echo manageSideNav('exportflashcards', 'Export flashcards', 'manage/export') ?></li>
        </ul>

      </div>
