/**
 * AjaxDialog handles dialogs with Ajax (loads markup) or pre existing markup.
 * Dialog interaction is either submit of the form, handled via ajax through AjaxPanel,
 * or front end only through binding of custom button/link events.
 * 
 * Notes
 * - If it's  from markup, dialog content is expected to be static (no AjaxPanel)
 *  
 * Options
 *   srcUrl                          Url for the AjaxPanel, with optional query string. No need for markup.
 *   useMarkup                       If markup already exists, it will be cloned for the dialog.
 *   events                          Listeners to register (see below), eg: { "onDialogInit": function(){} [, ...] }
 *   shadow                          Set to false to remove the dialog shadow (defaults true)
 *   modal                           Set to false to use non-modal dialog (defaults true)
 *   invisMask                       Set to true to make the modal mask invisible (defaults false)
 *   skin                            A class name that will be set on the main container, to skin a custom dialog
 *                                   (only srcUrl dialogs supported, not useMarkup) 
 * 
 * Notifications:
 *
 *   onDialogInit(t)                 "text/html" response, t is the TRON message, see AjaxPanel::onContentInit
 *   onDialogDestroy()               See AjaxPanel::onContentDestroy)
 *   onDialogResponse(o)             See AjaxPanel::onResponse() (o is YAHOO.util.Connect's Response Object)
 *   onDialogSubmit()                See AjaxPanel::onSubmitForm()
 * 
 * Status events:
 * 
 *   onDialogSuccess(t)              Dialog response or custom event returned a SUCCESS status (t = TRON msg)
 *   onDialogProgress(t)             Dialog response or custom event returned a PROGRESS status (t = TRON msg)
 *   onDialogFailed(t)               Dialog response or custom event returned a FAILED status (t = TRON msg)
 *
 * Usage:
 * 
 *   The ajax dialog response (text/html) must contain TRON message with properties
 *   to set the dialog to the required dimensions:
 *     
 *     dialogWidth   (int)
 *     dialogheight  (int)
 *     
 *   Static dialog should set the dimensions with class names on the outer div:
 *     
 *     <div id="my_dialog" class="dialog-width-400 dialog-height-200">
 *     
 *   If the response TRON status (App.Helper.TRON.STATUS_xxx) is:
 *     STATUS_FAILED   -> fire "onDialogFailed" event -> close dialog -> END
 *     STATUS_SUCCESS  -> fire "onDialogSuccess" event -> close dialog -> END
 *     STATUS_PROGRESS -> fire "onDialogProgress" event
 *     STATUS_EMPTY (no TRON message) -> doesn't fire the status events
 *   
 *     
 * Binding events (buttons, links, etc):
 * 
 *   Add class name:
 *     "dialog-success"    => fire onDialogSuccess and close dialog    
 *     "dialog-fail"       => fire onDialogFailed  and close dialog
 *     "dialog-close"      => close dialog
 *     
 *     "dialog-submit"     => submit the dialog's ajaxpanel (useful for form buttons styled with link tag)
 *
 *   Custom:
 *   
 *     // on() is a shortcut to the AjaxDialog's eventDelegator method
 *     myAjaxDialog.on("my-action", handler, scope);
 *     
 *     handler: function(e, el) {
 *       // OPTIONAL: return a dialog status to trigger the status events
 *       myAjaxDialog.handleDialogStatus(Core.Ui.AjaxDialog.STATUS_SUCCESS);
 *       
 *       return false;
 *     }
 *
 *
 * Static dialog event chain (option "useMarkup"):
 * 
 *   onDialogInit (on instance of AjaxDialog)
 *     -> onDialogFailed  (element of class name "dialog-fail")
 *       -> onDialogDestroy (dialog closes)
 *     -> onDialogSuccess (element of class name "dialog-success")
 *       -> onDialogDestroy (dialog closes)
 *     -> (do nothing)    (element of class name "dialog-cancel")
 *       -> onDialogDestroy (dialog closes)
 * 
 * Ajax event diagram:
 * 
 *   onDialogInit (only after first ajax request, not on instanciation)
 *     
 *     "text/html" response cycle:
 *     -> onDialogResponse (content loaded)
 *     -> onDialogDestroy  (before content replace)
 *     -> onDialogProgress / onDialogSuccess / onDialogFailed (status)
 *        If Success or Failed, dialog closes, END.
 *     -> onDialogInit     (re-init new content)
 *     
 *     "application/json" response (after html responses):
 *     -> onDialogResponse (json received with TRON status)
 *     -> onDialogProgress / onDialogSuccess / onDialogFailed (status)
 *        If Success or Failed:
 *        -> onDialogDestroy (dialog closes, END)
 *        
 * Usage:
 * 
 *   eventDel
 *     Instance of event delegator for "click" event on the dialog body.
 *     Use to attach custom events to the dialog. 
 *     
 *   getContentDiv()
 *     Returns the container div, use after dialog content is loaded.
 *
 *   show()
 *   
 * TODO
 * - if static dialog (useMarkup) contains a FORM, instance AjaxPanel?
 * 
 */
(function() {

  /**
   * Constructor.
   * 
   * @param {String} container   Id of containing element if using srcMarkup, otherwise set null
   * @param {Object} options
   */
  Core.Ui.AjaxDialog = function(container, options)
  {
    this.init.apply(this, arguments);
  };
  
  /**
   * Disable the first/last focus because of annoying :focus outline,
   * and eventually if we want a user friendly auto-focus of the first form field
   * we want more control than yui's default behaviour.
   * 
   * @see http://developer.yahoo.com/yui/docs/YAHOO.widget.Panel.html#property_YAHOO.widget.Panel.FOCUSABLE
   */
  if (YAHOO.widget && YAHOO.widget.Panel) {
    YAHOO.widget.Panel.FOCUSABLE = [];
  }
  
  // internal shorthands
  var 
    Y = YAHOO,
    Dom = Y.util.Dom,
    Event = Y.util.Event,
    AjaxPanel = Core.Ui.AjaxPanel,
    AjaxDialog = Core.Ui.AjaxDialog,
    
    // constants
    DIALOG_LOADING_W = 300,
    DIALOG_LOADING_H = 150,
    DIALOG_LOADING_CSS = 'AjaxDialogLoading',
    DIALOG_LOADING_HTML = '<div class="AjaxDialogLoading">&nbsp;</div>',
    DIALOG_PADDING_W = 20,
    DIALOG_PADDING_H = 20,
    
    INVISIBLE_MASK = "yui-invis-mask";

  // dialog status as returned by custom events (bind())
  AjaxDialog.STATUS_FAILED   = 0;
  AjaxDialog.STATUS_SUCCESS  = 1;
  AjaxDialog.STATUS_PROGRESS = 2;
    
  AjaxDialog.prototype = {

    options: null,
    
    eventDispatcher: null,
    
    events: {},

    // this dialog's event delegator
    eventDel: {},    
    
    // YAHOO.widget.Panel instance
    yPanel: null,
    
    // if loading content
    ajaxPanel: null,
    
    // container element for the dialog body 
    contentDiv: null,

    init: function(container, options)
    {
      var elDlg,
          dlgDimensions,
          that = this;
      
      Core.log('AjaxDialog.init()');
  
      options = !!options ? options : {};
  
      // set defaults
      this.options = options;
      this.options.close = false; // options.close === false ? false : true;
      this.options.modal = options.modal===false ? false : true;
      this.options.shadow = options.shadow===false ? false : true;
      this.options.invisMask = options.invisMask===true ? true : false;
      this.options.skin = options.skin || false; 
      //this.options.initContent = options.initContent === false ? false : true;

      if (options.useMarkup) {

        if (!Y.Lang.isString(container)) {
          Core.error("AjaxDialog.init() container must be string id");
        }

        // RHARRRRR YUI wtfwtf
        var elContainer = Core.Ui.get(container);
        elDlg = Core.Ui.get(container).cloneNode(true);
        elDlg.setAttribute('id', null);
        document.body.insertBefore(elDlg, document.body.firstChild);

        // set dimensions if specified
        dlgDimensions = [ 'auto', 'auto' ];
        if (/dialog-width-(\d+)/.test(elContainer.className)) {
          dlgDimensions[0] = RegExp.$1 + 'px';
        }
        if (/dialog-height-(\d+)/.test(elContainer.className)) {
          dlgDimensions[1] = RegExp.$1 + 'px';
        }
        
        this.container = elDlg;
      }
      else
      {
        // dynamically create empty dialog to load content
        elDlg = document.createElement('div');
        
        // insert the dialog INTO the #ie div (if present)
        var elParent = Core.Ui.get("ie") || document.body;
        elParent.insertBefore(elDlg, elParent.firstChild);
        this.container = elDlg;
        
        // set the width, leave out the height for YUI to adapt
        dlgDimensions = [ DIALOG_LOADING_W + 'px', null];
      }
      
      //
      this.yPanel = new Y.widget.Panel(elDlg, {
        modal: this.options.modal,
        draggable: true,
        fixedcenter: false,
        close: this.options.close,
        width: dlgDimensions[0],
        height: dlgDimensions[1],
        constraintoviewport:true,
        underlay: this.options.shadow ? "shadow" : "none",
        visible: false,
        /* optional */
        effect:{effect:Y.widget.ContainerEffect.FADE, duration: 0.2}
      });

      // ajax dialog's content div is an outer div with loading indicator
      // static dialog content div is 
      if (options.srcUrl) {
        // ajax container div
        this.yPanel.setBody(DIALOG_LOADING_HTML);
        this.contentDiv = Core.Ui.getFirstChild(this.yPanel.body);
      }
      else {
        this.contentDiv = this.container;
        this.contentDiv.style.display = 'block';
        /*
        Dom.setStyles(this.container, {
          visibility: "hidden",
          position: "absolute",
          display: "block"
        });*/
      }

      this.yPanel.render();

      // apply skin if provided
      if (this.options.skin !== false) {
        Dom.addClass(this.yPanel.element, this.options.skin);        
      }
      

      // register YUI Panel Custom Events
      //this.yPanel.hideEvent.subscribe(this.onPanelClose, this, true);

      // register events
      this.eventDispatcher = new Core.Ui.EventDispatcher();
      if (this.options.events) {
        var events = this.options.events, eventName;
        for (eventName in events) {
          this.eventDispatcher.connect(eventName, events[eventName]);
        }
      }
      
      // register default actions
      this.eventDel = new Core.Ui.EventDelegator(this.contentDiv, "click");
      this.eventDel.on("dialog-success", function(){ that.handleDialogStatus(AjaxDialog.STATUS_SUCCESS); return false; });
      this.eventDel.on("dialog-fail",    function(){ that.handleDialogStatus(AjaxDialog.STATUS_FAILED); return false; });
      this.eventDel.on("dialog-close",   function(){ that.onPanelClose(); return false; });
      this.eventDel.on("dialog-submit",  function(){
        if (!that.ajaxPanel || !that.ajaxPanel.getForm()) {
          App.alert("AjaxDialog()  using 'dialog-submit' without a form!");
        }
        that.ajaxPanel.send();
        return false;
      });
      
      // fire the init event for static dialog content
      if (this.options.useMarkup) {
        this.eventDispatcher.notify('onDialogInit');
      }
    },
    
    show: function()
    {
      if (this.options.useMarkup) {
        // show the static markup that was hidden
        Dom.setStyle(this.container, 'display', 'block');
      }

      //20091029+ manual center because "fixedcenter" is false
      this.yPanel.center(); 
      
      // enable our custom style that will make the modal mask transparent (see base.css)
      if (this.options.invisMask) 
      {
        Dom.addClass(document.body, INVISIBLE_MASK);
      }
      
      this.yPanel.show();

      if (this.options.srcUrl && !this.ajaxPanel)
      {
        this.ajaxPanel = new Core.Ui.AjaxPanel(this.contentDiv,
        {
          post_url: this.options.srcUrl,
          initContent: false,  // trigger onPanelInit only after content is loaded
          bUseShading: false,
          events: {
            onContentInit: Y.bind(this.onPanelInit, this),
            onContentDestroy: Y.bind(this.onPanelDestroy, this),
            onResponse: Y.bind(this.onPanelResponse, this)
          }
        });
        this.ajaxPanel.get();
      }

    },
    
    /**
     * Returns the ajax content div to work with the
     * dialog contents. Use from within one of the dialog events.
     * 
     * @return {HTMLElement}
     * 
     */
    getContentDiv: function()
    {
      return this.contentDiv;
    },

    /**
     * Add a custom event to the dialog, and bind to elements of given class name.
     * 
     * This is a proxy method for the dialog's EventDelegator.
     * 
     * @see   See EventDelegator for method signature.
     */
    on: function()
    {
      this.eventDel.on.apply(this.eventDel, arguments);
    },

    onPanelResponse:function(o)
    {
      Core.log("onPanelResponse(%o)", o);
      
      this.eventDispatcher.notify('onDialogResponse', o);

      // handle the dialog status events for JSON response
      var tron = this.ajaxPanel.getTron();
      if (tron) {
        this.handleTRONStatus(tron);
      }
    },
    
    onPanelInit:function()
    {
      Core.log('AjaxDialog::onPanelInit()');
      
      var tron = this.ajaxPanel.getTron(); // get HTML tron...

      // use the url present in the form in the underlying AjaxPanel
      if (this.ajaxPanel.getForm()) {
        // disable post_url now since we have a response with a form
        this.ajaxPanel.options.post_url = false;
      }

      // clear loading icon
      Dom.removeClass(this.contentDiv, DIALOG_LOADING_CSS);

      // handle dialog progress status
      if (this.handleTRONStatus(tron)) {
        return;
      }

      this.eventDispatcher.notify('onDialogInit', tron || false);
      
      // resize and recenter the dialog with new content
      var tv = tron.getReturnValues();
      if (tv.dialogWidth) {
        if (!this.donelala) {
          this.yPanel.cfg.setProperty('width', parseInt(tv.dialogWidth, 10) + DIALOG_PADDING_W + 'px');
          //this.yPanel.cfg.setProperty('height', parseInt(tv.dialogHeight, 10) + DIALOG_PADDING_H + 'px');
        } else {
          this.donelala = true;
        }
      }
      
      if (tv.dialogTitle) {
        this.yPanel.setHeader(tv.dialogTitle);
      }

      //20091029+ manually center after panel content is loaded
      this.yPanel.center();
      // this one works only if "fixedcenter" option is enabled
      //20091029- this.yPanel.doCenterOnDOMEvent();
    },

    /*
     * Handles status from TRON response  
     * 
     * @return {boolean}  True if the dialog is closed
     */
    handleTRONStatus:function(tron)
    {
      var status = tron.getStatus(),
          dialogStatus;
      
      switch (status) {
        case App.Helper.TRON.STATUS_FAILED:   dialogStatus = AjaxDialog.STATUS_FAILED; break;
        case App.Helper.TRON.STATUS_PROGRESS: dialogStatus = AjaxDialog.STATUS_PROGRESS; break;
        case App.Helper.TRON.STATUS_SUCCESS:  dialogStatus = AjaxDialog.STATUS_SUCCESS; break;
        default:
          App.warn('AjaxDialog::handleTRONStatus() invalid status');
          break;
      }
      return this.handleDialogStatus(dialogStatus, tron);
    },

    /**
     * Handles status response (from bound action or the dialog ajax response).
     * 
     * Closes the dialog in the success/fail cases, fires the status-related events.
     * 
     * @param status
     * 
     * @return {boolean}  True if the dialog is closed
     */
    handleDialogStatus:function(dialogStatus, tron)
    {
      Core.log('AjaxDialog.handleDialogStatus(%o)', dialogStatus);

      if (dialogStatus === AjaxDialog.STATUS_SUCCESS)
      {
        // success : dismiss dialog, notify event 
        this.eventDispatcher.notify('onDialogSuccess', tron || false);
        this.onPanelClose();
        return true;
      }
      else if (dialogStatus === AjaxDialog.STATUS_FAILED)
      {
        // failed : dismiss dialog, notify event
        this.eventDispatcher.notify('onDialogFailed', tron || false);
        this.onPanelClose();
        return true;
      }
      else if (dialogStatus === AjaxDialog.STATUS_PROGRESS) 
      {
        // progress : do nothing (form submission cycle or other custom)
        this.eventDispatcher.notify('onDialogProgress', tron || false);
      }

      return false;
    },
    
    /**
     * Maps to AjaxPanel::onContentDestroy()
     * 
     */
    onPanelDestroy:function()
    {
      this.eventDispatcher.notify('onDialogDestroy');
    },

    /**
     * Close and cleanup
     * DISABLED*** bound to YUI "hideEvent" to catch the Panel close button
     *
     */
    onPanelClose: function()
    {
    //  Core.log("AjaxDialog::onPanelClose()");
      this.destroy();
    },

    destroy: function()
    {
      // don't run twice
      if (this.destroyed) {
        return;
      }
      else {
        this.destroyed = true;  
      }

      this.eventDispatcher.notify('onDialogDestroy');
      
      if (this.ajaxPanel) {
        this.ajaxPanel.destroy();
        this.ajaxPanel = null;
      }
      
      // clean events
      this.eventDel.destroy();
      //Event.purgeElement(this.contentDiv, true);
      
      this.yPanel.hide();
      this.yPanel.destroy();

      // remore our custom class
      if (this.options.invisMask) {
        Dom.removeClass(document.body, INVISIBLE_MASK);
      }
      
    }
    
  };

})();
