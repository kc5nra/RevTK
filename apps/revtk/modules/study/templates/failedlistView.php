<div class="layout-study">

	<?php include_partial('SideColumn', array('framenum' => 0)) ?>

  <div class="col-main col-box col-box-top">

		<div class="app-header">
			<h2>Restudy Kanji List</h2>
			<div class="clear"></div>
		</div>
		
		<div class="section">
			<p>	This is the complete list of all kanji in the failed stack.</p>
			<p>	The kanji characters are not shown here, so that you can test your memory before clicking on a keyword in the list below.</p> 
			<p>	To clear a kanji from the failed stack and move it back into the review cycle, click the "Learned" button in the Study page for that character.</p> 
		</div>
		
		<div id="FailedListTable">
			<?php include_component('study', 'FailedListTable') ?>
		</div>

<script type="text/javascript">
var FailedListPage =
{
	initialize:function()
	{
		this.ajax_table = new uiWidgets.AjaxTable($('FailedListTable'));
	}
}
Event.observe(window, 'load', function(){ FailedListPage.initialize() });
</script>

  </div>

</div>
