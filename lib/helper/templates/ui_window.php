<?php echo tag('div', $options, true) ?>
  <div class="window-container">
    <div class="window-top">
      <div class="window-handle"></div>
      <a href="#" class="close" title="Close Window"></a>
    </div>
    <div class="window-body">
<?php echo $content; ?>
    </div>
  </div>
  <div class="underlay">
  <?php ui_box_rounded('uiWindowUnderlay') ?>
  &nbsp;
  <?php echo end_ui_box() ?>
  </div>
</div>
