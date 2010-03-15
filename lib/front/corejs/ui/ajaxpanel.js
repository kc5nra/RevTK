/*jslint forin: true */
/*global window, Core, App, YAHOO, alert, console, document */
/**
 * Handles communication of content between client and server for a
 * portion of a html page (text/html), or JSON data (application/json).
 * 
 * Notes:
 * - During the Ajax communication, the portion of the page is covered with a
 *   layer that blocks mouse clicks. By default it is not visible (fully transparent),
 *   but can be set to shading with option 'bUseShading'.
 * - Content can be sent as a typical HTTP request or as JSON data.
 * - The response can be JSON or HTML.
 * 
 * By default the panel uses HTML requests. The server receives GET/POST requests
 * and returns HTML as for standard html pages, except no <head> or <body> tags
 * should be returned.
 * 
 * FORM submission:
 * 
 * - By default, the first FORM found in the panel will be serialized and sent via Ajax
 *   when it is submitted (onsubmit event). To use another FORM, set the "form" option.
 * - If an onSubmitForm listener was registered, then the listener must handle the form
 *   submission by calling get(), post() or send().
 * - If post_url is set, and there is a form, it overrides the form's action attribute,
 *   otherwise the form's action attribute is used
 * - By default form is submitted with send(), so it uses the method attribute (get/post)
 * 
 * If not using a form, you must provide "post_url". If the response is HTML, it will
 * replace the panel contents just like the forms. You can use the onResponse event to
 * handle non html responses.
 * 
 * 
 * Options:
 * 
 *   container          Container element where content is loaded
 *   options
 *     form             True by default, will pick the first FORM element in the panel.
 *                      To use another FORM element than the first one in the panel, specify a class name (string)
 *                      to match the form to use (must be child of container), or false to disable
 *                      the form submit binding.
 *     post_url         Url for requests, if not set will look for action attribute of a FORM element
 *     events           Handlers for notifications to subscribe to (see below)
 *     bUseLayer        Cover the area with a layer that blocks mouse clicks during ajax (defaults TRUE)
 *     bUseShading      If set and true, the container is darkened with a opacity layer while ajax is
 *                      in process, otherwise a transparent layer is used (defaults FALSE).
 *     initContent      By default, the onContentInit notification is NOT fired when the AjaxPanel is instanced,
 *                      set to true to fire onContentInit when the panel is created
 *     
 *     TODO ajaxIndicator    Provide an object with the App.Ui.AjaxIndicator interface, defaults to App.Ui.AjaxIndicator class.            
 * 
 * Notifications:
 * 
 *   onContentInit()                 Called to initialize content of the panel
 *   onContentDestroy()              Called before content is replaced with HTML ajax response
 *   onResponse(o)                   Ajax response received, BEFORE content is replaced (if response HTML)
 *                                    (o is YAHOO.util.Connect's Response Object)
 *   onFailure(o)                    Ajax response received with HTTP code NOT 2xx, AFTER the display of the error/rety message.
 *                                    (o is YAHOO.util.Connect's Response Object, check "status" and "statusText" etc.)
 *   onSubmitForm(e)                 A form is submitted (e is the event object). Use YAHOO.util.Event.getTarget(e)
 *                                   to identify the form element if needed. Return false to cancel the default submission,
 *                                   (the listener may do a manual get/post/send with extra parameters), return a truthy value
 *                                   (or nothing) to proceed with the default form submission.
 * 
 * Methods:
 *   getForm()                       Returns the form element that is currently observed.
 *   setForm(form)                   Set form to serialize with the next get/post/send() request (string id | HTMLElement)
 *   get(parameters)                 Do a GET request, accepts additional parameters in hash or query string format
 *   post(parameters)                Do a POST request, accepts additional parameters in hash or query string format
 *   send(parameters)                Do either a GET or POST depending on the form's method attribute, accepts additional parameters
 *                                   This method requires a form present in the panel!
 *   connect()                       Call after a failed request (get, post, send) to retry the last request
 *   
 *   
 * Event chain with text/html responses:
 * 
 *   onContentInit (unless option "initContent" is false)
 *      Content-Type "text/html" responses:
 *      -> onResponse -> onContentDestroy (replace) -> onContentInit
 *      Any other response type (eg. "application/json"):
 *      -> onResponse -> DO NOTHING (manual calls to get/post/send)
 * 
 * @version  2009.07.17 (uses new EventDispatcher, onSubmitForm return value)
 * @author   Fabrice Denis
 */
(function() {
 
  Core.Ui.AjaxPanel = Core.createClass();
  
  // internal shorthands
  var Y = Core.YUI,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      AjaxPanel = Core.Ui.AjaxPanel;
    
  AjaxPanel.prototype = {

    // Custom Events instances
    events: {},
    
    // Form to seriliaze with next get() or post() call
    serializeForm: false,

    // Set true after at least one succesful html content request 
    contentLoaded: false,
    
    /**
     * 
     * 
     * @param {String|HTMLElement} container
     * @param {Object} options
     */
    init: function(container, options)
    {
      Core.log('AjaxPanel.init() options %o ',options);
  
      options = !!options ? options : {};
  
      // set defaults
      this.options = options;
      this.options.form = (options.form && options.form !== true) ? options.form : true;
      this.options.bUseLayer = options.bUseLayer || true;
      this.options.bUseShading = options.bUseShading || false;
      this.options.initContent = options.initContent === true ? true : false;
  
      Core.log('AjaxPanel.init() this.options %o', this.options);
  
      this.container = Dom.get(container);
      if (!this.container) {
        Core.error("AjaxPanel.init() invalid container");
      }
  
      this.evtCache = new Core.Ui.EventCache();
  
      this.ajaxRequest = null;
  
      // register events
      this.eventDispatcher = new Core.Ui.EventDispatcher();
      if (this.options.events) {
        var events = this.options.events, eventName;
        for (eventName in events) {
          this.eventDispatcher.connect(eventName, events[eventName]);
        }
      }

      this.initContent();
    },
    
    destroy: function()
    {
      if (this.contentLoaded) {
        this.eventDispatcher.notify('onContentDestroy');        
      }

      if (this.shadeLayer) {
        this.shadeLayer.destroy();
      }
      this.evtCache.destroy();
      this.eventDispatcher.destroy();
    },
  
    initContent: function()
    {
      Core.log('AjaxPanel.initContent()');

      // Attach an event to FORMs that will dispatch a "onSubmit" event.
      var elForm = (this.serializeForm = this.getForm());

      if (elForm) {
        this.evtCache.addEvent(elForm, 'submit', Core.bind(this.submitFormEvent, this));
      }
  
      if (this.contentLoaded || this.options.initContent === true) {
        this.eventDispatcher.notify('onContentInit');
      }
    },

    replaceContent: function(sHtml)
    {
    // Core.log('AjaxPanel.replaceContent()');
  
      this.evtCache.destroy();

      if (this.contentLoaded)
      {
        this.eventDispatcher.notify('onContentDestroy');        
      }
      
      this.container.innerHTML = sHtml;
      this.contentLoaded = true;
      
      // setup UI for forms, tables, etc.
      this.initContent();
    },
  
    /**
     * Sets form to use with the next request (serialize data & action attribute).
     * 
     * @param  {String|HTMLElement}  elForm   Form id or element
     */
    setForm: function(elForm)
    {
      elForm = Dom.get(elForm);
      if (!elForm.nodeName || elForm.nodeName.toLowerCase()!=='form') {
        throw new Error("setForm() argument 0 is not a form element");
      }

      this.serializeForm = elForm;      
    },
  
    /**
     * Returns the form element that is currently observed.
     * 
     * @return mixed  FORM element, or null if none is observed
     */
    getForm: function()
    {
      if (this.options.form === true)
      {
        return this.container.getElementsByTagName('form')[0];
      }
      else if (Y.lang.isString(this.options.form))
      {
        // return the first form that matches the class name
        var form = Dom.getElementsByClassName(this.options.form, 'form', this.container)[0];
        if (!form) {
          Core.error("AjaxPanel.getForm() form not found (by class name)");
        }
        return form;
      }
      
      return this.options.form;
    },
  
    /**
     * 
     * @param {Object} e   YUI Event
     */
    submitFormEvent: function(e)
    {
      var form, skipSubmit = false;
      
      Core.log('AjaxPanel.submitFormEvent(%o) Form %o', e, Event.getTarget(e));

      // if listener exists, and it returns false, do not auto-submit
      if (this.eventDispatcher.hasListeners('onSubmitForm')) {
        skipSubmit = false === this.eventDispatcher.notify('onSubmitForm', e);
      }

      if (!skipSubmit) {
        form = this.getForm();
        this.setForm(form);
        this.send();
      }

      Event.stopEvent(e);
    },
  
    /**
     * Do a GET request with optional parameters to add to the request.
     * 
     * @param {Object} oData   A hash with variables, or a query string, or undefined (optional)
     */
    get: function(oData)
    {
      this.prepareConnect(oData, 'get');
    },
  
    /**
     * Do a POST request with optional parameters to add to the request.
     * 
     * @param {Object} oData   A hash with variables, or a query string, or undefined (optional)
     */
    post: function(oData)
    {
      this.prepareConnect(oData, 'post');
    },

    /**
     * Do a GET or POST request, using the active form's "method" attribute. 
     * 
     * @param {Object} oData   A hash with variables, or a query string, or undefined (optional) 
     */
    send: function(oData)
    {
      var form = this.getForm() || Core.error("AjaxPanel.send()  Requires a form");
      var method = form.getAttribute('method') || 'post';
      this.prepareConnect(oData, method);
    },
    
    /**
     * 
     * @param {Object} oData    A hash with post variables, or a query string, or undefined
     * @param {string} sMethod  Method name 'post' or 'get'
     */
    prepareConnect: function(oData, sMethod)
    {
      var post_url,
          form = false,
          connectObj = {};
  
      // optional parameters
      if (!Y.lang.isUndefined(oData)) {
        connectObj.parameters = oData;
      }
      
      // form to serialize, if any
      if (this.serializeForm !== false) {
        form = connectObj.form = this.serializeForm;
      }

      post_url = this.options.post_url || (form ? form.action : false);
      if (!post_url) {
        Core.error('AjaxPanel.post() need post_url and/or a FORM element');
      }
      
      // dont send multiple requests at the same time
      if (this.ajaxRequest && this.ajaxRequest.isCallInProgress()) {
        Core.warn('Previous AjaxRequest still in progress (or bug?)');
        return;
      }
  
    //  Core.log('AjaxPanel.prepareConnect(%o, %s) FORM %o', oData, sMethod, form);

      connectObj.url = post_url;
      connectObj.method = sMethod;
      
      // start connection
      this.connect(connectObj);
    },
  
    /**
     * Establish the server connection with the current post() parameters.
     * Call with arguments to establish the connection settings.
     * Call with empty arguments to reconnect with the last settings, in case
     * the connection failed or timed out.
     * 
     * Connection object:
     *   url           Url for AjaxRequest
     *   method        Method for AjaxRequest
     *   form          Form to serialize (optional)
     *   parameters    Extra GET/POST parameters 
     */
    connect: function(oConnect)
    {
      if (oConnect) {
        this.connection = oConnect;
      }
  
      if (!this.connection) {
        Core.error('AjaxPanel.connect() No connection object.');
      }
  
      Core.log("connect ",this.options,oConnect);
      var options = {
        method:       this.connection.method,
        form:         this.connection.form,
        parameters:   this.connection.parameters,
        nocache:      true,
        timeout:      this.options.timeout,

        success:      this.ajaxOnSuccess,
        failure:      this.ajaxOnFailure,

        events: {
          // ajax indicator
          onStart:    this.ajaxOnStart,
          onComplete: this.ajaxOnComplete
        },
        scope:        this

      };

      this.ajaxRequest = new Core.Ui.AjaxRequest(this.connection.url, options);
    },
  
    /**
     * YUI Connect custom event.
     * 
     * @param {String} eventType
     * @param {Object} args
     */
    ajaxOnStart: function(eventType, args)
    {
    //  Core.log('AjaxPanel.ajaxOnStart(%o)', args);
      
      // layer
      if (this.options.bUseLayer) {
        // create layer, then reuse it
        if (!this.shadeLayer) {
          this.shadeLayer = new Core.Ui.ShadeLayer({
            element:    this.container,
            glass_mode: !this.options.bUseShading
          });
        }
        this.shadeLayer.show();
      }
      else {
        this.shadeLayer = null;
      }
  
      // create a new uiAjaxIndicator because it is added inside the container
      // and the container content can be replaced
      this.ajaxIndicator = new Core.Ui.AjaxIndicator({container: this.container});
      this.ajaxIndicator.show();
    },
    
    /**
     * YUI Connect custom event.
     * 
     * @param {String} eventType
     * @param {Object} args
     */
    ajaxOnComplete: function(eventType, args)
    {
    //  Core.log('AjaxPanel.ajaxOnComplete(%o)', args);

      //  Core.log('AjaxPanel.ajaxOnComplete()');
      //var response = args[0];
      
      // hide loading indicator
      this.ajaxIndicator.destroy();
  
      if (this.shadeLayer) {
        this.shadeLayer.hide();
      }
    },
    
    /**
     * Success handler.
     * 
     * @param {Object} oAjaxResponse   YUI Connect response object, augmented by AjaxRequest (responseJSON, ...)
     */
    ajaxOnSuccess: function(oAjaxResponse)
    {
      Core.log('AjaxPanel.ajaxOnSuccess(%o)', oAjaxResponse);

      this.eventDispatcher.notify('onResponse', oAjaxResponse);
      
      // handle HTML response
      if (oAjaxResponse.getResponseHeader['Content-Type'].indexOf('text/html') === 0 &&
        oAjaxResponse.responseText.length)
      {
        this.replaceContent(oAjaxResponse.responseText);
      }

      // cleanup
      this.ajaxRequest = null;
    },
    
    /**
     * Failure handler.
     * 
     * @param {Object} oAjaxResponse   YUI Connect response object, augmented by AjaxRequest (responseJSON, ...)
     */
    ajaxOnFailure: function(oAjaxResponse)
    {
      Core.log('AjaxPanel.ajaxOnFailure(%o)', oAjaxResponse);
    
      // transaction aborted (timeout)
      if (oAjaxResponse.status === -1)
      {
        // show the timeout message
        this.showErrorMessage('Oops! Timed out.');
        return;
      }
  
      var sMessage = 'Oops! Error '+oAjaxResponse.status+' "'+oAjaxResponse.statusText+'".';
      this.showErrorMessage(sMessage);
  
      this.eventDispatcher.notify('onFailure', oAjaxResponse);
      
      // cleanup
      this.ajaxRequest = null;
    },
    
    /**
     * Display a message in place of the ajax indicator,
     * with a "Retry" link.
     * 
     * @param {Object} sMessage
     */
    showErrorMessage: function(sMessage)
    {
      this.ajaxErrorIndicator = new Core.Ui.AjaxIndicator({
        container: this.container, 
        message: sMessage + ' <a href="#" style="font-weight:bold;color:yellow;">Retry</a>'
      });
      this.ajaxErrorIndicator.show();
      
      var elMessage = this.ajaxErrorIndicator.getElement();
      var elRetryLink = elMessage.getElementsByTagName('a')[0];
      
      var retry = function(oEvent) {
        Core.log('AjaxPanel.ajaxRetryEvent()');
        this.ajaxErrorIndicator.destroy();
        this.connect();
      };

      Event.on(elRetryLink, 'click', retry, this, true);
    }
  };

})();
