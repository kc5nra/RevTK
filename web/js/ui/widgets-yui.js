/*! uiWidgets Javascript Framework (c) Fabrice Denis - http://kanji.koohii.com */
/**
 * uiWidgets
 *  !!AjaxTable
 *  !!SelectionTable
 *  !!uiWindow
 *  TabbedView
 *  !!FilterStd
 * 
 * @requires  uibase-yui.js
 * @author    Fabrice Denis
 */

var uiWidgets = {};


/**
 * AjaxTable uses uiAjaxPanel to allow sorting and paging through a uiSelectTable
 * php component, using ajax updates.
 * 
 * How it works:
 * 
 *   By default, the uiSelectTable component adds sorting and paging variables as a query
 *   string in the links in the table heads and the pager, eg: '...?sort=created_on&order=0&page=1'.
 *   This allows sorting and paging via default GET requests on the whole page.
 *   
 *   When javascript is enabled, uiAjaxTable intercepts clicks on the links,
 *   and passes the query string as an ajax GET request instead. The action on the server
 *   simply returns the uiSelectTable component view alone, when receiving a POST request.
 * 
 * Options:
 * 
 *   elAjaxPanel    Container element for uiAjaxPanel. This is usually a DIV that wraps
 *                  around the view of the uiSelectTable component (the pager and table).
 * 
 */
(function(){

  uiWidgets.AjaxTable = function()
  {
    this.initialize.apply(this, arguments);
  };

  var Y = YAHOO,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      AjaxTable = uiWidgets.AjaxTable; 

  AjaxTable.prototype = {

    initialize:function(elAjaxPanel)
    {
      this.elAjaxPanel = elAjaxPanel;
      this.oAjaxPanel = new uiAjaxPanel(elAjaxPanel,{
        events: {
          onSubmitForm: Y.bind(this.onSubmitForm, this)
        }
      });
  
      this.evtCache = new uiEventCache();
      this.evtCache.addEvent(elAjaxPanel, 'click', Y.bind(this.evPanelClick, this));
    },
  
    destroy:function()
    {
      this.evtCache.destroy();
    },
  
    /**
     * Detect a click on a table head for column sorting (or a uiPager link),
     * and use the query string in the href attribute of the link as post variables.
     * 
     * @todo  Improve the column sorting link matching with a unique class instead
     *        of matching a string subset 'sort' (to match sort, sortasc, sortdesc).
     *        Requires updating uiSelectTable php component.
     * 
     * @param {Object} oEvent   Prototype event.
     */
    evPanelClick:function(oEvent)
    {
      var el = oEvent.findElement('a');
      if (!el) {
        return;
      }
  
      var isUiTableHead = el.className.indexOf('sort') >= 0 && el.parentNode.nodeName.toLowerCase()==='th';
      var isUiPagerLink = !isUiTableHead && el.up('.uiPagerDiv');
  
      if (isUiTableHead || isUiPagerLink)
      {
        var sQuery = $(el).readAttribute('href');
        var pos = sQuery.indexOf('?');
        var oQueryParams = (pos >= 0) ? sQuery.substr(pos+1).toQueryParams() : {};
  
        var oForm = this.elAjaxPanel.down('form');
        var oParams = oForm ? oForm.serialize(true) : {};
        Object.extend(oParams, oQueryParams);
        
        this.oAjaxPanel.get(oParams);
        oEvent.stop();
      }
    },
  
    /**
     * This is a placeholder to prevent default FORM submission of uiAjaxPanel.
     * 
     */
    onSubmitForm:function(oEvent)
    {
    }
  };

})();


/**
 * uiSelectionTable
 * 
 * Adds selectable row behaviour to a uiAjaxTable.
 * 
 * @todo  Document, generalize
 */


(function() {

  uiWidgets.SelectionTable = function() {
    this.initialize.apply(this, arguments);
  };
  
  var Y = YAHOO,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      SelectionTable = uiWidgets.SelectionTable; 

  SelectionTable.prototype = {

    /**
     * 
     */
    selection: {},
    
    initialize:function(elContainer)
    {
      this.elContainer = elContainer;
      this.ajaxTable = new uiWidgets.AjaxTable(elContainer);
  
      this.evtCache = new uiEventCache();
      this.evtCache.addEvent(elContainer, 'click', Y.bind(this.evClick, this));
    },
    
    destroy:function()
    {
      this.evtCache.destroy();
      this.ajaxTable.destroy();
    },
    
    evClick:function(e)
    {
      var row, inputs, element = Event.getTarget(e);
  
      if (element.className==='checkbox')
      {
        row = element.up('tr');
        inputs = row.getElementsByTagName('input');
        this.setSelection(row, inputs, element.checked);
      }
      else if (element.className==='chkAll')
      {
        var elTable = this.elContainer.getElementsByTagName('table')[0];
        var i, rows = elTable.tBodies[0].rows;
        var check = element.checked;
        for (i = 0; i < rows.length; i++)
        {
          row = rows[i];
          inputs = row.getElementsByTagName('input');
          if (inputs[1].checked !== check)
          {
            inputs[1].checked = check;
            this.setSelection(row, inputs, check);
          }
        }
      }
      else
      {
        // if clicked in a row, select it
        row = oEvent.findElement('tr');
        if (row)
        {
          elChk = row.down('input.checkbox');
          if (elChk)
          {
            elChk.click();
            oEvent.stop();
          }
        }
      }
    },
    
    setSelection:function(row, inputs, check)
    {
      // set value
      inputs[0].value = check ? '1' : '0';
      // set highlight
      Dom[check ? 'addClass' : 'removeClass'](row, 'selected');
    }
  };

})();


/**
 * uiWindow
 * 
 * Options:
 *   left, top        Absolute position
 *   width            Should be specified for ie6 absolute positioning of elements
 *                    (Optional, defaults to 'auto')
 *   opacity          Window borders opacity (defaults to false = 0 = 100% opacity)
 *   events           Listeners for notifications ( name => function )
 *   
 * Methods:
 * 
 *   show()           Display window
 *   hide()           Hide window
 *   close()          Close the window, remove event listeners
 * 
 *   getBodyElement() Returns (Prototype extended) window content div (.window-body).
 *   
 * Notifications:
 * 
 *   onWindowClose    The close button has been clicked, called BEFORE destroy() method
 * 
 * @author   Fabrice Denis
 * @package  Ui/Widgets
 */
(function() {

  uiWidgets.uiWindow = function() {
    this.initialize.apply(this, arguments);
  };
  
  var Y = YAHOO,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      uiWindow = uiWidgets.uiWindow; 

  uiWindow.prototype = {

    initialize:function(elWindow, options)
    {
      this.elWindow   = Dom.get(elWindow);
      this.elBody     = Dom.getElementsByClassName('window-body', 'div', this.elWindow)[0];
      this.elUnderlay = Dom.getElementsByClassName('underlay', 'div', this.elWindow)[0];

      // set defaults
      this.options = options;
      this.options.opacity = options.opacity || 0.5; // false = 100% opaque
  
      // set window borders opacity
      Dom.setStyles(this.elUnderlay, { opacity: this.options.opacity });
  
      // register events
      this.eventDispatcher = new App.Ui.EventDispatcher();
      if (options.events)
      {
        for (var sEvent in options.events) {
          this.eventDispatcher.connect(sEvent, options.events[sEvent]);
        }
      }
  
      // titlebar bar close button
      this.evtCache = new App.Ui.EventCache();
      this.elTitleBar = Dom.getElementsByClassName('window-top', 'div', this.elWindow)[0];
      var elCloseButton = Dom.getElementsByClassName('close', 'a', this.elTitleBar)[0];

      if (elCloseButton)
      {
        var that = this;
        this.evtCache.addEvent(elCloseButton, 'click', function(e)
        {
          that.eventDispatcher.notify('onWindowClose');
          Event.stopEvent(e);
        });
      }
      
      // position window
      Dom.setStyles(this.elWindow, {
        left:     this.options.left+'px',
        top:      this.options.top+'px',
        width:    this.options.width ? this.options.width+'px' : 'auto',
        position: 'absolute',
        zIndex:   2
      });
      
      if (this.options.draggable && typeof(YAHOO)!=='undefined')
      {
        this.dragdrop = new Y.util.DD(this.elWindow);
        var elHandle = Dom.getElementsByClassName('window-handle', 'div', this.elWindow)[0];
        this.dragdrop.setHandleElId(elHandle);
      }
    },
    
    show:function()
    {
      // position window
      Dom.setStyles(this.elWindow, { display: 'block' });
    },
    
    hide:function()
    {
      Dom.setStyles(this.elWindow, { display: 'none' });
    },
    
    destroy:function()
    {
      if (this.dragdrop)
      {
        this.dragdrop.unreg();
      }
  
      this.evtCache.destroy();
    },
  
    close:function()
    {
      this.hide();
      this.destroy();
    },
  
    getBodyElement:function()
    {
      return this.elBody;
    }
  };

})();


/**
 * uiTabbedView
 * 
 * Setting up with the ui_tabs() helper:
 *   For each link definition in ui_tabs(), add the class name "uiTabbedView-xxx" in the options.
 *   Use the same format, as ID attribute on the corresponding view divs (<div id="uiTabbedView-xxx").
 *   Optionally wrap the views with a container <div class="uiTabbedBody"> to match the uiTabs styles.
 * 
 * Structure:
 *   Example structure with the base styles from uiTabs and uiTabbedBody
 *   
 *   <div class="uiTabs">
 *     <? ui_tabs() output ?>
 *   </div>
 *   <div class="uiTabbedBody">
 *     <div id="uiTabbedView-viewid">
 *        View one
 *     </div>
 *     <div id="uiTabbedView-viewid">
 *        View two
 *     </div>
 *     ...
 *   </div>
 *   
 * Options:
 * 
 *   elContainer    The wrapper divs of the ui-tabs, usually this is the element with the id
 *                  that was passed to the ui_tabs() helper (as options).
 *   
 *   events         An array of listeners (name => function) to respond to tab events:
 *   
 * Tab events:
 * 
 *   onTabClick(view_id)
 *                  Fires after a tab is activated and visible.
 *   onTabFocus(view_id)
 *                  Fires when a tab is clicked on, before the corresponding view is made
 *                  visible and the tab is made active. First argument is the view id
 *                  (the "xxx" part in the class name "uiTabbedView-xxx").
 *   onTabBlur(view_id)
 *                  Fires when a tab looses focus, before the new one is activated.
 * 
 * @requires  uibase
 */
 
(function(){

  uiWidgets.TabbedView = function() {
    this.init.apply(this, arguments);
  };
  
  var Y = YAHOO,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      TabbedView = uiWidgets.TabbedView;

  TabbedView.prototype =
  {
    init:function(elContainer, events)
    {
      elContainer = Dom.get(elContainer);
      
       this.evtCache = new App.Ui.EventCache();
      
      // register events
      this.eventDispatcher = new App.Ui.EventDispatcher();
      if (events)
      {
        for (var sEvent in events) {
          this.eventDispatcher.connect(sEvent, events[sEvent]);
        }
      }
  
      this.views = {};
      this.currentView = null;
  
      var aLIs = elContainer.getElementsByTagName('li');
      var i;
      for (i=0; i < aLIs.length; i++)
      {
        var elLI = Dom.get(aLIs[i]);
        var elLink = elLI.getElementsByTagName('a')[0];
        var matches = /uiTabbedView-(\S+)/.exec(elLink.className);
        if (matches)
        {
          var elViewDiv = Dom.get(matches[0]) || alert('uiTabbedView: bad view id');
          var sViewId = matches[1];
          this.views[sViewId] = { li: elLI, div: elViewDiv };
          
          this.evtCache.addEvent(elLink, 'click', Y.bind(this.evTabClick, this, sViewId));
    
          if (Dom.hasClass(elLI, 'active'))
          {
            this.currentView = sViewId;
          }
        }
      }
  
    },
    
    destroy:function()
    {
      this.evtCache.destroy();
    },
    
    // that: undefined value from addListener!
    evTabClick:function(oEvent, that, sViewId)
    {
      // blur last tab
      if (this.currentView!==null && this.currentView!==sViewId)
      {
        Dom.removeClass(this.views[this.currentView].li, 'active');
        this.eventDispatcher.notify('onTabBlur', this.currentView);
      }
  
      // focus new tab
      if (sViewId !== this.currentView)
      {
        this.currentView = sViewId;
        this.eventDispatcher.notify('onTabFocus', sViewId);
        Dom.addClass(this.views[sViewId].li, 'active');
  
        for (viewid in this.views)
        {
          this.views[viewid].div.style.display = (viewid===sViewId) ? 'block' : 'none';
        }
      }
      
      this.eventDispatcher.notify('onTabClick', sViewId);
      
      Event.stopEvent(oEvent);
    }
  };
})();


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
 */
(function(){

  uiWidgets.FilterStd = function()
  {
    this.initialize.apply(this, arguments);
  };

  var Y = YAHOO,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      FilterStd = uiWidgets.FilterStd,
      
      // constants
      ACTIVE = 'active';

  /**
   * Notification when switch is clicked and activated.
   */
  FilterStd.SWITCH_EVENT = 'onSwitch';

  FilterStd.prototype =
  {
    initialize:function(elContainer, events)
    {
      this.evtCache = new App.Ui.EventCache();
  
      this.eventDispatcher = new App.Ui.EventDispatcher();
      if (events)
      {
        for (var sEvent in events) {
          this.eventDispatcher.connect(sEvent, events[sEvent]);
        }
      }
  
      this.currentTab = null;
      this.tabs = {};
  
      var i, tabs = elContainer.getElementsByTagName('a');
      for (i=0; i < tabs.length; i++)
      {
        var elLink = Dom.get(tabs[i]);
        var matches = /uiFilterStd-(\S+)/.exec(elLink.className);
        if (matches)
        {
          var sTabId = matches[1];
          this.tabs[sTabId] = { element: elLink };
          Event.on(elLink, 'click', Y.bind(this.eventHandler, this, sTabId));
          if (Dom.hasClass(elLink, ACTIVE))
          {
            this.currentTab = sTabId;
          }
        }
      }
    },
    
    destroy:function()
    {
      this.evtCache.destroy();
    },
    
    eventHandler:function(oEvent, sTabId)
    {
      if (this.currentTab === sTabId)
      {
        // don't fire the notification twice
        Event.stopEvent(oEvent);
        return;
      }
      
      if (this.currentTab) {
        Dom.removeClass(this.tabs[this.currentTab].element, ACTIVE);
      }
  
      this.currentTab = sTabId;
  
      Dom.addClass(this.tabs[sTabId].element, ACTIVE);
      
      this.eventDispatcher.notify(FilterStd.SWITCH_EVENT, sTabId);
      
      Event.stopEvent(oEvent);
    }
  };
  
})();
