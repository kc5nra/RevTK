<h2>Layout</h2>

<p>Examples:
<?php pre_start() ?>
  // Disable layout decoration
  $this->setLayout(<em>false</em>);

  // use the template file /apps/demo/templates/<em>layout_name</em>View.php
  $this->setLayout('<em>layout_name</em>');
<?php pre_end() ?>

<p>View this page with a <?php echo link_to('different layout', 'test/layout?layout_name=other_layout') ?>.</p>

<p>Go <?php echo link_to('back', '@homepage') ?>.</p>

