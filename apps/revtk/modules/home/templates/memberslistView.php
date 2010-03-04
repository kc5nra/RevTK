<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  	<div class="col-box col-box-top">

		<div class="app-header">
			<h2><?php echo link_to('Home', '@homepage') ?> <span>&raquo;</span> Members reviewing in the past 30 days</h2>
			<div class="clearboth"></div>
		</div>

<?php if (coreConfig::get('app_forum_url')): ?>
		<p>	Note: see the forum <?php echo link_to('user list ', coreConfig::get('app_forum_url').'/userlist.php') ?>
		 		for a searchable list of all registered members.</p>
<?php endif ?>          

		<div id="MembersListComponent">
			<?php include_component('home', 'MembersList') ?>
		</div>

	</div>
  </div>

</div>


<script type="text/javascript">
Event.observe(window, 'load', function(){
	var ajaxTable = new uiWidgets.AjaxTable($('MembersListComponent'));
});
</script>
