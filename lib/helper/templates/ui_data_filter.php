<div class="uiDataFilter">
  <div class="corner-left"></div>
  <div class="corner-right"></div>
<?php if ($caption): ?>
  <h4><?php echo $caption ?></h4>
<?php endif ?>
  <ul>
<?php foreach($links as $link): ?>
  <li<?php echo $link['active'] ? ' class="active"' : '' ?>><?php echo link_to('<span>'.$link['name'].'</span>', $link['internal_uri'], $link['options']) ?></li>
<?php endforeach ?>
  </ul>
  <div class="clear"></div>
</div>
