/*jslint forin: true */
/*global window, Core, App, YAHOO, alert, console, document */
/**
 * EventCache keeps track of events and allows to clear them all at once
 * when the object is destroyed.
 * 
 * To do:
 * - add variable number of extra arguments to addEvent()
 * 
 * Uses:
 * - Clearing events fixes a memory leak in old versions of IE.
 * - It is useful for ajax components, by clearing the events the content
 *   can be rendered more or less disabled until it is replaced with the result
 *   of an ajax call.
 * 
 * Methods:
 *   init(sDebug)
 *   addEvent(element, eventTypes, fn[, context])
 *   destroy()
 *   
 * Examples:
 *   this.evtCache = new Core.Ui.EventCache();
 *   this.evtCache.addEvent(el, "click", clickEvent, this);
 *   
 *   this.evtCache.addEvent(el, ["mouseenter", "mouseleave"], clickEvent, this);
 * 
 */
(function(){

  var Y = Core.YUI, 
      Event = Y.util.Event,
      EventCache = Core.Ui.EventCache = Core.createClass();

  EventCache.prototype = {

    sId: null,
    
    eCache: null,

    init: function(sId)
    {
      this.sId = sId || '';
      this.eCache = [];
    },
    
    addEvent: function(element, sEventType, fn)
    {
      Event.addListener(element, sEventType, fn);
      this.push(element, sEventType, fn);
    },

    /**
     * Bind multiple events to one event handler function.
     * 
     * @param {Object} element
     * @param {Object} aEventTypes   An array of event types
     * @param {Object} fn
     */
    addEvents: function(element, aEventTypes, fn)
    {
      var i;
      for (i = 0; i < aEventTypes.length; i++)
      {
        this.addEvent(element, aEventTypes[i], fn);
      }
    },
  
    push: function(element, sEventType, handler) {
      this.eCache.push({oElem:element, evType:sEventType, fn:handler});
    },
  
    destroy: function()
    {
      if (this.eCache)
      {
        // Core.log('EventCache.destroy('+this.sId+') '+this.eCache.length+' events');
        for(var i=this.eCache.length-1; i>=0; i--) {
          var evc = this.eCache[i];
          Event.removeListener(evc.oElem, evc.evType, evc.fn);
        }
        // free up references to the elements
        this.eCache = [];
      }
    }
  };

})();

