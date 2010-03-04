<?php use_helper('Widgets') ?>

<?php #slot('inline_styles', get_slot('inline_styles') . $graph->getInlineStyles()) ?>

	<div class="actions">
<?php if ($expired_total > 0): ?>
		<?php echo $me->getReviewLink('<span><strong>'.$expired_total.'</strong> due cards</span>', array('type' => 'expired', 'box' => 'all'), array('class' => 'uiFBtn uiFBtnOr')) ?>
<?php endif ?>
<?php if ($untested_cards > 0): ?>
		<?php echo $me->getReviewLink('<span><strong>'.$untested_cards.'</strong> new cards</span>', array('type' => 'untested'), array('class' => 'uiFBtn uiFBtnBl')) ?>
<?php endif ?>
<?php if ($restudy_cards > 0): ?>
		<?php echo link_to('<span><strong>'.$restudy_cards.'</strong> restudy cards</span>', 'study/failedlist', array('class' => 'uiFBtn uiFBtnRe')) ?>
<?php endif ?>
		<div class="clear"></div>
	</div>

	<div class="filters">
		<?php
			$links = array(
				array('Simple', '#', array('class' => 'uiFilterStd-s')),
				array('Full', '#', array('class' => 'uiFilterStd-f'))
			);
			echo ui_filter_std('View:', $links, array('class' => 'mode-toggle', 'active' => 0));
		?>
	</div>

	<div class="clear"></div>

<div class="svg-outer-div">
	<div class="svg-inner-div"></div>
	<div class="mode-toggle mode-simple">
		<a href="#" class="mode-simple">Simple</a>
	</div>
</div>

<?php use_helper('Form') ?>
<?php echo input_hidden_tag('json', coreJson::encode($chart_data), array('class' => 'json')) ?>

<script>
var chartdata = <?php echo coreJson::encode($chart_data) ?>;
</script>
