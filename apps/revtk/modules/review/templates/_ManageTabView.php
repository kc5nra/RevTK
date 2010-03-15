
<div id="manage-tabs">
  <?php echo ui_tabs(array(
        array('Lorem',   '#', array('class' => 'uiTabbedView-ManageX')),
        array('Ipsum',   '#', array('class' => 'uiTabbedView-ManageY')),
        array('Opossum', '#', array('class' => 'uiTabbedView-ManageZ'))
      ), 0) ?>
  <div class="uiTabbedBody">
    <div id="uiTabbedView-ManageX">

      <div class="app-header">
        <h2>Manage flashcards</h2>
        <div class="clear"></div>
      </div>

      <p>View one.</p>

    </div>

    <div id="uiTabbedView-ManageY" style="display:none">
      View two.
    </div>

    <div id="uiTabbedView-ManageZ" style="display:none">
      View three.
    </div>

  </div>
  <div class="clear"></div>
</div>
