<?php use_helper('Date') ?>

  <div class="col-news col-box col-box-top">
  	
	<div class="app-header">
		<h2>News by Month</h2>
		<div class="clearboth"></div>
	</div>

	<div class="content">
		<ul>
			<?php foreach (SitenewsPeer::getArchiveIndex() as $p): ?>
			<li><?php echo link_to(format_date(mktime(0,0,0,$p->month,1,$p->year), "M Y"),
					'news/index?year='.$p->year.'&month='.$p->month).' ('.$p->count.')' ?></li>
			<?php endforeach ?>
		</ul>
	</div>
  </div>
