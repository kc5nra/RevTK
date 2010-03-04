<?php use_helper('Form', 'Widgets') ?>

<div id="manage-cards" class="layout-rindex">
	<div class="col-main col-box">

		<div class="app-header">
			<h2><a href="Home">Home</a> <span>&raquo;</span> Manage flashcards</h2>
			<div class="clear"></div>
		</div>

		<div class="uiSideTabs">
			
			<?php include_partial('SideNav', array('active' => 'addcustom')) ?>
			
			<div class="views">
				<div id="manage-view">
					
					<h3>Add Custom Flashcard Selection</h3>

					<div class="ajax">
						<?php include_partial('AddCustom') ?>
					</div>

				</div>
			</div>
			<div class="clear"></div>
		</div>

	</div>

</div>
