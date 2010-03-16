<?php
/**
 * uiWidgets.FilterStd template (a widget to switch between multiple options)
 * 
 * @uses  css/ui/widgets.css
 * @see   js/ui/widgets.js (uiWidgets.FilterStd)
 */
?>
<?php echo tag('div', $options, true) . "\n" ?>
<?php if (!empty($label)): ?>
  <span class="lbl"><?php echo $label ?></span>
<?php endif ?>
  <span class="tb">
    <span class="lr"><?php
      foreach($links as $link)
      {
        $name = $link[0];
        $internal_uri = $link[1];
        $options = isset($link[2]) ? $link[2] : array();
        echo link_to($name, $internal_uri, $options);
      }
    ?></span>
  </span>
  <div class="clear"></div>
</div>
