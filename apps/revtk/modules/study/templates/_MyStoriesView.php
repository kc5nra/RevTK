<?php use_helper('Widgets') ?>
<?php use_stylesheet('/css/2.0/widgets.css'); /*export button*/ ?>

<?php $num_stories = StoriesPeer::getStoriesCounts($_user->getUserId()); ?>

<div class="app-header">
  <h2>My Stories</h2>
  <div class="clear"></div>
</div>

<div class="section">
  <p>  This list shows all stories you have edited on the website, both <em>private</em> and <em>public</em> ones.
    The kanji are hidden until you point the cursor at the story, so that it doesn't interfere with your recall.
  </p>

  <?php #The page should be printer friendly, try "Print Preview" in your browser.</p> ?>

  <div style="width:100%;position:relative;">
    Stories : <strong><?php echo $num_stories->private ?></strong> private,
    <strong><?php echo $num_stories->public ?></strong> public,
    <strong><?php echo $num_stories->total ?></strong> total.
    <div style="position:absolute;right:0;top:-3px;"><?php echo ui_ibtn('Export to CSV', 'study/export', array('icon' => 'export')) ?></div>
  </div>
</div>

<?php echo ui_data_filter('Order by:', $sort_links, $sort_active) ?>

<div id="MyStoriesComponent">
  <?php include_component('study', 'MyStoriesTable', array()) ?>
</div>

<script type="text/javascript">
var MyStoriesPage =
{
  initialize:function()
  {
    this.ajax_table = new uiWidgets.AjaxTable($('MyStoriesComponent'));
  }
}
Event.observe(window, 'load', function(){ MyStoriesPage.initialize() });
</script>
