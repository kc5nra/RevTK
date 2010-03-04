/*jslint forin: true */
/*global window, Core, App, alert, console, document */
/**
 * AjaxTable uses AjaxPanel to allow sorting and paging through a uiSelectTable
 * php component, using ajax updates.
 * 
 * How it works:
 * 
 *   By default, the uiSelectTable component adds sorting and paging variables as a query
 *   string in the links in the table heads and the pager, eg: '...?sort=created_on&order=0&page=1'.
 *   This allows sorting and paging via default GET requests on the whole page.
 *   
 *   When javascript is enabled, AjaxTable intercepts clicks on the links with class name
 *   "table-page" (paging, rows per page) and "table-sort" (table head). The query string
 *   part of the link's href attribute is routed as a POST request to an ajax controller.
 *   The ajax controller returns just the table view.
 * 
 * Options:
 * 
 *   container    Container element for the AjaxPanel. This is usually a DIV that wraps
 *                around the view template of the php table component (and pager, etc).
 * 
 * @author   Fabrice Denis
 * @version  2009/11/30 (yui3)
 */
(function() {

  Core.Ui.AjaxTable = function() {
    this.init.apply(this, arguments);
  };

  var
    Y         = Core.YUI,
    Ui        = Core.Ui,
    AjaxTable = Core.Ui.AjaxTable;

  AjaxTable.prototype =
  {
    container: null,
    
    /**
     * Constructor.
     * 
     * @param {String|Object} elContainer   Container id, HTMLElement or YUI Node  
     */
    init: function(container)
    {
      this.container = Ui.get(container);
      this.oAjaxPanel = new Core.Ui.AjaxPanel(this.container, {
        events: {
          onSubmitForm: Core.bind(this.onSubmitForm, this)
        }
      });

      this.evtDel = new Core.Ui.EventDelegator(container, "click", "at");
      this.evtDel.on("table-page", this.evPanelClick, this);
      this.evtDel.on("table-sort", this.evPanelClick, this);
    },
  
    destroy: function()
    {
      this.evtDel.destroy();
    },
  
    /**
     * Detect a click on a table head for column sorting (or a uiPager link),
     * and use the query string in the href attribute of the link as post variables.
     * 
     * Query string should always start with a "?" (with or without url)
     * 
     * @param {YUI.Event.Facade} e  Event object
     * @param {HTMLElement} el
     */
    evPanelClick: function(e, el)
    {
      var query, pos, params;

      if ((query = e.target.getAttribute("href")) && (pos = query.indexOf('?')) >= 0)
      {
        params = query.substr(pos + 1);
        this.oAjaxPanel.send(params);
        return false;
      }

      return true;
    },
  
    /**
     * This is a placeholder to prevent default FORM submission of uiAjaxPanel.
     * 
     */
    onSubmitForm: function(e)
    {
    }
  };

})();
