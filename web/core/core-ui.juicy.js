/**
 * Main include for Core.Ui demos in Core Documentation
 * 
 * Juicer build (manual):
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config apps/core/config/juicer.config.php --infile web/js/core/core-ui.juicy.js
 *   
 */

/* =require from "%CORE%" */
/* =require "/core/core.js" */

/* =require from "%CORE%" */
/* =require "/ui/ui.js" */
/* =require "/ui/eventcache.js" */
/* =require "/ui/eventdelegator.js" */
/* =require "/ui/eventdispatcher.js" */
/* =require "/ui/shadelayer.js" */
/* =require "/ui/ajaxindicator.js" */
/* =require "/ui/ajaxrequest.js" */
/* =require "/ui/ajaxpanel.js" */
/* !require "/ui/ajaxtable.js" */

/* !require "/widgets/widgets.js" */
/* !require "/widgets/selectiontable/selectiontable.js" */
/* !require "/widgets/tabbedview/tabbedview.js" */
/* !require "/widgets/filterstd/filterstd.js" */
/* !require "/widgets/window/window.js" */

// App skeleton for the Core Javascript demos
var App =
{
  // EventDelegator
  evtDel: null,
  
  // Page init code
  ready: null,
  
  init: function()
  {
    this.evtDel = new Core.Ui.EventDelegator(document.body, "click");
    
    if (this.ready)
    {
      Core.log('App.ready()');
      this.ready();
    }
  }
};

Core.ready(function(){ App.init(); });
