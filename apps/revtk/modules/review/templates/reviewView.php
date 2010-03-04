<div class="layout-review">
	
	<div class="col-main col-box col-box-top">

		<div class="app-header">
			<h2><?php echo link_to('Review','@overview') ?> <span>&raquo;</span> <?php echo $title ?></h2>
			<div class="clearboth"></div>
		</div>
		
		<div id="uiFcReview-container">
<?php include_partial($uiFR->getPartialName(), $uiFR->getPartialVars()) ?>
		</div>

	</div>

</div>
