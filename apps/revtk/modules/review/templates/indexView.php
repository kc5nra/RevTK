<?php use_helper('Widgets') ?>

<?php #DBG::warn($_user->getLocalPrefs()->get('review.graph.filter')) ?>

<div class="layout-rindex">
  <div class="col-main col-box">

    <div class="app-header">
      <h2><?php echo link_to('Home','@homepage') ?> <span>&raquo;</span> Review flashcards</h2>
      <div class="clear"></div>
    </div>

    <div class="summary">
      <span class="total"><strong><?php echo $total_flashcards ?></strong> kanji flashcards</span>&nbsp;
      ( <?php echo link_to('browse detailed list','review/flashcardlist') ?> - <a href="@manage">manage flashcards</a> )
    </div>

    <div class="filterstop">

      <?php
        $links = array(
          array('ALL', '#', array('class' => 'uiFilterStd-all')),
          array('RTK1', '#', array('class' => 'uiFilterStd-rtk1'))
        );
         if (ReviewsPeer::getCountRtK3($_user->getUserId()) > 0)
        {
           $links[] = array('RTK3', '#', array('class' => 'uiFilterStd-rtk3'));
         }
        switch($filter)
        {
          case 'rtk1': $active = 1; break;
          case 'rtk3': $active = 2; break;
          default: $active = 0; break;
        }
        echo ui_filter_std('Filter:', $links, array('id' => 'rtk-filter', 'active' => $active));
      ?>

    </div>

    <div class="clear"></div>

    <div id="view-pane-all" class="rtk-filter-pane" style="<?php ui_display($filter==='all') ?>">
      <div class="leitner-svg-chart">
        <?php if ($filter==='all'): ?>
          <?php include_component('review', 'LeitnerChart') ?>
        <?php else: ?>
          <div class="not-loaded-yet"></div>
        <?php endif ?>
      </div>
      <!--p> php echo link_to('Review fullscreen (dev)','review/fullscreen') ?-->
    </div>
    <div id="view-pane-rtk1" class="rtk-filter-pane" style="<?php ui_display($filter==='rtk1') ?>">
      <div class="leitner-svg-chart">
        <?php if ($filter==='rtk1'): ?>
          <?php include_component('review', 'LeitnerChart') ?>
        <?php else: ?>
          <div class="not-loaded-yet"></div>
        <?php endif ?>
      </div>
    </div>
    <div id="view-pane-rtk3" class="rtk-filter-pane" style="<?php ui_display($filter==='rtk3') ?>">
      <div class="leitner-svg-chart">
        <?php if ($filter==='rtk3'): ?>
          <?php include_component('review', 'LeitnerChart') ?>
        <?php else: ?>
          <div class="not-loaded-yet"></div>
        <?php endif ?>
      </div>
    </div>

  </div>

</div>

<script type="text/javascript">
function graphModeToggle(elToggle, oSVGDisplay)
{
  function onSwitch(id)
  {
    var mode = id==='f' ? rkLeitnerSVG.prototype.MODE_FULL : rkLeitnerSVG.prototype.MODE_SIMPLE;
    oSVGDisplay.toggleViewMode(mode);
  }

  this.filter = new uiWidgets.FilterStd(elToggle, { 'onSwitch': onSwitch });
}

Event.observe(window, 'load', function()
{
  var filterViews = {
    'all':  { loaded:false, url:"<?php echo url_for('review/AjaxLeitnerGraph') ?>", filter:'all' },
    'rtk1': { loaded:false, url:"<?php echo url_for('review/AjaxLeitnerGraph') ?>", filter:'rtk1' },
    'rtk3': { loaded:false, url:"<?php echo url_for('review/AjaxLeitnerGraph') ?>", filter:'rtk3' }
  };
  filterViews['<?php echo $filter ?>'].loaded = true;
  
  var filter = new uiWidgets.FilterStd($('rtk-filter'),
  {
    'onSwitch': function(sViewId)
    {
      // toggle view pane
      var viewid;
      for (viewid in filterViews)
      {
        $('view-pane-'+viewid).style.display = viewid===sViewId ? 'block' : 'none';
      }
      
      // load contents
      var oView = filterViews[sViewId];
      if (!oView.loaded)
      {
        var elPanelSVG = $('view-pane-'+sViewId).down('.leitner-svg-chart');
        oView.ajaxPanel = new uiAjaxPanel(elPanelSVG,
        {
          bUseShading: false, 
          post_url: oView.url, 
          events: {
            onContentInit: (function(elPanel)
            {
              return function()
              {
                var elJSON = elPanel.down('input.json');
                if (elJSON)
                {
                  oView.loaded = true;
                  var chartdata = elJSON.value.evalJSON(true);
                  oSVGDisplay2 = new rkLeitnerSVG(elPanel, chartdata);
                  new graphModeToggle(elPanel.down('.mode-toggle'), oSVGDisplay2);
                }
              };
            })(elPanelSVG)
          }
        });
        oView.ajaxPanel.get({ filter: oView.filter });
      }
    }
  });

  var elSVG = $('view-pane-<?php echo $filter ?>').down('.leitner-svg-chart');
  var oSVGDisplay = new rkLeitnerSVG(elSVG, chartdata);
  var toggle = new graphModeToggle(elSVG.down('.mode-toggle'), oSVGDisplay);

});
</script>

