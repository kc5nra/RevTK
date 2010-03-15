/*jslint forin: true */
/*global window, Core, App, YAHOO, alert, console, document */
/**
 * FilterStd manages a "switch" where the user can toggle between multiple buttons.
 * 
 * Setup:
 *   Each A tag within the container needs a classname in the form "uiFilterStd-xxx".
 *   The "xxx" identifier will be passed to the event listeners.
 * 
 * Events:
 *   onSwitch(id)     Fires AFTER the tab is active, id is the identifier from the classname.
 * 
 * @author   Fabrice Denis
 * @version  2009/11/30 (yui3)
 */
(function(){

  Core.Widgets.FilterStd = function() {
    this.init.apply(this, arguments);
  };

  var Y = Core.YUI,
      Ui = Core.Ui,
      FilterStd = Core.Widgets.FilterStd,
      ACTIVE = 'active';

  /**
   * Notification when switch is clicked and activated.
   */
  FilterStd.SWITCH_EVENT = 'onSwitch';

  FilterStd.prototype =
  {
    init: function(elContainer, events)
    {
      this.evtCache = new Core.Ui.EventCache();
  
      this.eventDispatcher = new Core.Ui.EventDispatcher();
      if (events)
      {
        for (var sEvent in events) {
          this.eventDispatcher.connect(sEvent, events[sEvent]);
        }
      }
  
      this.currentTab = null;
      this.tabs = {};
  
      Ui.getNode(elContainer).all('a').each(function(link)
      {
        var matches = /uiFilterStd-(\S+)/.exec(link._node.className);
        if (matches)
        {
          var sTabId = matches[1];
          this.tabs[sTabId] = link;
          
          this.evtCache.addEvent(link, "click", Core.bind(this.eventHandler, this, sTabId));
          
          if (link.hasClass(ACTIVE)) {
            this.currentTab = sTabId;
          }
        }
      }, this);
    },
    
    destroy: function()
    {
      this.evtCache.destroy();
    },
    
    eventHandler: function(e, sTabId)
    {
      if (this.currentTab === sTabId) {
        // don't fire the notification twice
        e.halt();
        return;
      }
      
      if (this.currentTab) {
        this.tabs[this.currentTab].removeClass(ACTIVE);
      }
  
      this.currentTab = sTabId;
  
      this.tabs[sTabId].addClass(ACTIVE);
      
      this.eventDispatcher.notify(FilterStd.SWITCH_EVENT, sTabId);
      
      e.halt();
    }
  };
  
})();