/*! uiView (c)2008 Fabrice Denis - http://kanji.koohii.com */

/**
 * uiView
 * 
 * @package   UiParts
 * @author    Fabrice Denis
 * @copyright (c)2008 Denis Fabrice
 */

var RevTK =
{
  AJAX_CONTAINER_CLASS:    'uiAjaxContainer',

  controllers:        // all relative urls to be appended to RevTK.baseUrl
  {
    // base url WITH trailing slash
    BASE_URL:      'http://www.supersite.com/',
    // views
    EXAMPLE_CONTROLLER:  'exampleController.ajax'
  },

  currentView: null,      // uiView instance
  currentViewParams: null,  // the view's url and query parameters { url: string , params:{prop:value, ...}} 

  initialize:function(contentDiv)
  {
    // setup default content div for all instances of uiView (prototype!)
    uiView.prototype.contentDiv = $(contentDiv);
    
    // setup ajax
    this.ajaxInitialize();

    // initialize view content
    this.currentView = new uiView({ url:'/wooooops/wooops.ajax', params:null });
    this.currentView.initializeContent();
  },

  /* handles ajax */

  ajaxInitialize:function()
  {
    var that = this;
    this.ajaxLoadDiv = $('proto-ajax-loading') || alert('proto-ajax-loading DIV not found');

    Ajax.Responders.register(
    {
      onCreate:function()
      {
        that.ajaxLoadDiv.style.display = 'block';
      },
      onComplete:function()
      {
        that.ajaxLoadDiv.style.display = 'none'; // reset to hidden
      }
    });
  },

  ajaxHandleFailure:function(response)
  {
    // cf. http://www.prototypejs.org/api/ajax/response
    alert('The server didn\'t respond. Please try again shortly.');
    
    /*
    var timeOutOccured = response.getHeader('RevTK-SESSIONEXPIRED');
    if (response.status==500 && timeOutOccured)
    {
      RevTK.redirectToTimeoutPage();
    }
    else
    {
      RevTK.redirectToErrorPage();
    }
    */
  },

  /* this method redirects to the timeout page (used by RevTK.ajaxHandleFailure) */
  redirectToTimeoutPage:function()
  {
    //window.onbeforeunload = null; // could be set by uiforms.js
    //window.location = RevTK_GLOBALS.BASEURL_CONTEXTPATH + 'sessionExpired.htm';
  },

  /* this method redirects to the system error page (used by RevTK.ajaxHandleFailure) */
  redirectToErrorPage:function()
  {
    //window.onbeforeunload = null; // could be set by uiforms.js  
    //window.location= RevTK_GLOBALS.BASEURL_CONTEXTPATH + 'systemError.htm';    
  },

  /* updating the view */

  // returns false if the view change was cancelled due to unsaved form
  loadView:function(sUrl, params, bUsePost)
  {
    var sMethod = bUsePost ? 'post' : 'get';

    // prevent loading another view if the user forgot to save changes
    if (this.currentView &&
      this.currentView.uiFormInstance &&
      // FIXME:: bPageModified moved into uiSection
      this.currentView.uiFormInstance.bPageModified)
    {
      if (!confirm(RevTK_GLOBALS.CONFIRM_UNSAVED_CHANGES))
      {
        return false;
      }
      // stop last section from pushing data on server due to outside click
      RevTK.bFreezeForm = true;
    }

    // { params: id=<nnnn>, type=orgArea  [ , refreshTable=1 ] }
    //
    // HARDCODED now for orgArea views
    var that = this;
    new Ajax.Request(sUrl,
    {
      method: sMethod,
      parameters: params || {},
      onSuccess: function(transport)
      {
      //  console.log('RevTK::loadView()::response()');  

        // destroy last view (purge events, etc.)
        if (that.currentView)
          that.currentView.destroy();
        
        // replace content
        that.currentViewParams = { url:sUrl, params:params };
        that.currentView = new uiView(that.currentViewParams);
        that.currentView.replaceContent(transport.responseText);
      },
      onFailure:function(response)
      {
        RevTK.ajaxHandleFailure(response);
      }
    });
    
    return true;
  },
  
  // reload the view and tell the server to discard cached changes
  viewDiscardChanges:function()
  {
    var sUrl = this.currentViewParams.url;
    var oNewParams = Object.clone(this.currentViewParams.params);
    oNewParams.discardchanges = 1;
    this.loadView(sUrl, oNewParams);
  },

  /* misc helpers */
  
  errorMessageBox:function(sMsg)
  {
    return '<div class="messagebox msgbox-error"><p>'+sMsg+'</p></div>';
  }
  
}


/* ----------------------------- */
/* uiView : handles main content */
/* ----------------------------- */
// The view object is re-instantiated every time the main content is replaced via ajax.
// This object handles attaching and destroying events for the tables and forms handling.


var uiView = Class.create();
uiView.prototype =
{
  contentDiv: null,

  // oParams.sHtml, oParams.url, oParams.params
  initialize:function(oParams)
  {
  //  console.log('uiView::initialize()');

    // check content div was set
    if (!this.contentDiv) {
      alert('Invalid uiView contentDiv');
    }

    // variables
    this.controllerUrl = oParams.url;      // url from the ajax call that loaded the view
    this.controllerParams = oParams.params;    // query parameters from the ajax call that loaded the view

    this.uiAjaxTableInstances = {};        // remember uiAjaxTable objects so we can cleanup events
    this.uiFormInstance = null;

  },

  // replaceContent is used by Ajax calls when the View's html is replaced
  replaceContent:function(sHtml)
  {
  //  console.log('uiView::replaceContent()');

    // replace content
    this.contentDiv.innerHTML = sHtml;

    // setup UI for forms, tables, etc.
    this.initializeContent();
  },

  // this can be called directly on page load, if the page already contains a View
  initializeContent:function()
  {
    //
    this.divGlobalMessage = $('JsViewGlobalMessage');
    // get parameters
    var jsViewParamsDiv = dom.getElementsByClassName(this.contentDiv,'div','JsViewParams')[0];
    if (!jsViewParamsDiv) {
      alert('uiView - "jsViewParams" div missing');
      return;
    }
    this.jsViewParams = jsViewParamsDiv ? dom.getHiddenParams(jsViewParamsDiv) : null;
    if (this.jsViewParams)
    {
      if (this.jsViewParams.globalerrormsg) {
        this.showGlobalMessage(this.jsViewParams.globalerrormsg, true);
      }
      else if (this.jsViewParams.globalsuccessmsg) {
        this.showGlobalMessage(this.jsViewParams.globalsuccessmsg, false);
      }
    }

    // table sorting events
    this.initTables();

    // form handling
    RevTK.bFreezeForm = false;
    this.uiFormInstance = new uiForm(this);

    // top links (!!reuse uiForm's event cache)
    this.initTopLinks();
  },

  initTopLinks:function()
  {
    var toplinksDiv = dom.getElementsByClassName(this.contentDiv,'div','title-options')[0];
    if (toplinksDiv)
    {
      var links = toplinksDiv.getElementsByTagName('a');
      for (var i=0; i<links.length; i++)
      {
        if (/JsUseCtl-(\w+)/.test(links[i].className))
        {
          var sControllerId = RegExp.$1; // name of property in JsViewParams
          this.uiFormInstance.evtCache.addEvent(links[i], 'click', this.useControllerEvent.bindAsEventListener(this, sControllerId));
        }
        else if (/JsCustom-(\w+)/.test(links[i].className))
        {
          var sCustomMethodId = RegExp.$1; // name of method in RevTK global object
          this.uiFormInstance.evtCache.addEvent(links[i], 'click', this.useCustomMethodEvent.bindAsEventListener(this, sCustomMethodId));
        }
      }
    }
  },
  
  // event for "JsUseCtl-xxxx" links in page title area : directly load a view
  useControllerEvent:function(e, sControllerId)
  {
  //console.log('uiView::topLinksEvent('+sControllerId+')');

    var sControllerUrl = this.jsViewParams[sControllerId] || alert('topLink controller missing');
    new Ajax.Request(sControllerUrl, {
      method: 'post',
      parameters: this.jsViewParams,
      onSuccess: function(transport)
      {

        // destroy last view (purge events, etc.)
        if (RevTK.currentView)
          RevTK.currentView.destroy();
        // replace content
        RevTK.currentView = new uiView(RevTK.currentViewParams);
        RevTK.currentView.replaceContent(transport.responseText);

      },
      onFailure:function(response)
      {
        RevTK.ajaxHandleFailure(response);
      }
    });
  },
  
  // event for "JsCustom-xxxx" links in title area : call method on global RevTK object
  useCustomMethodEvent:function(e, sCustomMethodId)
  {
    if (typeof(RevTK[sCustomMethodId])!=='function')
    {
      alert("uiView:: custom method call not in global RevTK object");
    }
    else
    {
      RevTK[sCustomMethodId].apply(RevTK);
    }
    Event.stop(e);
    return false;
  },

  showGlobalMessage:function(message, isError)
  {
    var div = this.divGlobalMessage;
    if (!div){
      alert('Missing div#JsViewGlobalMessage for response message');
    }
    div.style.display='block';
    div.innerHTML = '<div class="messagebox msgbox-'+(isError?'error':'success')+'"><p>'+message+'</p></div><div class="clear"></div>';
  },
  
  clearGlobalMessage:function()
  {
    var div = this.divGlobalMessage;

    if (div){
      div.style.display='none';
    }
  },
  
  destroy:function()
  {
  //  console.log('uiView::destroy()');

    // cleanup events before replacing the main page content, to prevent memory leaks in IE6
    this.uiFormInstance.destroy();

    // purge events from uiAjaxTable instances
    var sTableId;
    for (sTableId in this.uiAjaxTableInstances){
      this.uiAjaxTableInstances[sTableId].destroy();
    }
  },

  errorMessage:function(sMsg)
  {
    this.updateMainContentView(RevTK.errorMessageBox(sMsg));
  },


  // look for NON EDITABLE tabular data in the view and create uiAjaxTable instances to handle paging, sorting
  // EDITABLE tabular data is handled by uiForm > uiSection
  initTables:function()
  {
  //  console.log('uiView::initTables()');
    var i;
    var tables = dom.getElementsByClassName(this.contentDiv, 'table', uiWidgets.AjaxTable.prototype.TABULARDATA_CLASS);    
    for (i=0; i < tables.length; i++)
    {
      var table = tables[i];
      var bIsEditable = dom.getParent(table,'div','expd-content')!==null;
      if (!bIsEditable)
      {
        table.id = 'uiTableInstId'+i;
        var tableInst = this.uiAjaxTableInstances[table.id] = new uiWidgets.AjaxTable(tables[i], this);
      }
    }
  }
}


/* ----------------------------- */
/* uiAjaxTable : extends uiTable class with paging and sorting via Ajax updates */
/* ----------------------------- */

var uiAjaxTable = Class.create();
uiAjaxTable.prototype = {

  TABULARDATA_CLASS: 'uiTabular',

  initialize:function(table, uiViewInst)
  {
  //  console.log('uiAjaxTable::initialize()');

    this.table = table;
    this.uiViewInst = uiViewInst;        // contains controllerUrl for Ajax update
    this.evtCache = new uiEventCache('uiAjaxTable');
    this.ajaxContainer = null;
    this.jsData = {};

    // we need a wrapper div for html updates via Ajax    
    this.ajaxContainer = dom.getParent(table,'div',RevTK.AJAX_CONTAINER_CLASS);
    if (!this.ajaxContainer) alert('uiAjaxTable::initialize() missing wrapper DIV '+RevTK.AJAX_CONTAINER_CLASS);

    // does table use a custom controller?
    var jsDataDiv = dom.getElementsByClassName(this.ajaxContainer,'div','JsData')[0];
    this.jsData = jsDataDiv ? dom.getHiddenParams(jsDataDiv) : {};
    if (!this.jsData.controller) {
      alert('uiAjaxTable:: jsData.controller not set!');
    }

    this.initTable(table);
  },

  initTable:function(table)
  {
    this.uiTableInst = new uiTable(table);

    // attach paging events
    var tPaging = dom.getElementsByClassName(this.ajaxContainer,'div','JsPaging')[0];
    if (tPaging) {
      this.uiTableInst.evtCache.addEvent(tPaging, 'click', this.pagingEvent.bindAsEventListener(this));
    }

    // attach sorting event
    var thead = table.tHead;
    if (thead) {
      this.uiTableInst.evtCache.addEvent(thead, 'click', this.sortingEvent.bindAsEventListener(this));
    }
  },

  destroy:function()
  {
    this.uiTableInst.destroy();
  },
  
  updateContents:function(sHtml)
  {
  //  console.log('uiAjaxTable::updateTable()');
  
    // purge events  before replacing
    this.uiTableInst.destroy();

    // replace whole table
    this.ajaxContainer.innerHTML = sHtml;

    // reattach events, note: restore id on table!
    var table = dom.getElementsByClassName(this.ajaxContainer,'table',this.TABULARDATA_CLASS)[0];
    this.initTable(table);
  },

  ajaxRequest:function(params)
  {
    // use POST + custom controller if provided, otherwise use GET + the view's controller (old method)
    var that = this;
    var url = this.jsData.controller;
    var sMethod = this.jsData.controller ? 'post' : 'get';
    new Ajax.Request(url, {
      method: sMethod,
      parameters: params,
      onSuccess: function(transport) {
        that.updateContents(transport.responseText);
      },
      onFailure:function(){
        that.ajaxContainer.innerHTML = RevTK.errorMessageBox('uiAjaxTable::ajaxRequest() :: Error accessing url:<br/>'+url);
      }
    });
  },

  // for sorting, we call the same Java controller that loaded the view,
  // pass the query parameters that are in the column head link (th > a)
  // PLUS 'refreshTable=1' to tell the controller to rebuild only the table
  sortingEvent:function(e)
  {
    var elem = Event.element(e);
alert('xxxxyo');
    var th = dom.getParent(elem, 'th');
    if (!th)
      return;


    if (/(^|\s)sort[a-z]*/.test(th.className))
    {
      // a sortable column
      // refresh table via ajax, use the url of the controller we saved with the view
      // the link within the table head contains all the parameters needed
      
      var params = th.getElementsByTagName('a')[0].getAttribute('href');
      this.ajaxRequest(params);
    }

    return false;
  },

  // see sortingEvent()
  pagingEvent:function(e)
  {
    var elem = Event.element(e);
    
    if (elem.nodeName.toLowerCase()=='a')
    {
      var params = elem.getAttribute('href');
      this.ajaxRequest(params);
    }

    Event.stop(e);
    return false;
  }
}


dom.addEvent(window,'load', function() { RevTK.initialize('main'); } );
