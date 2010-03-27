/*! uiBase framework (c) Fabrice Denis - http://kanji.koohii.com */
/*jslint forin: true */
/*global uiConsole, window, YAHOO, alert, console, document */
/**
 * "App" global namespace.
 * 
 * YUI extensions:
 *  YAHOO.bind(fn, scope [, args, ...] ) 
 *  YAHOO.util.Dom.setStyles(element, hash)
 *  YAHOO.Array.each(array, fn [, context])
 *
 * App.Toolkit
 *  toQueryString(obj)
 *  
 * uiConsole
 *  log(message [, args])
 * 
 * UI Base Toolkit.
 * 
 *  App.Ui.EventDispatcher (legacy code, should use YUI's custom events... TODO)
 *  App.Ui.EventCache
 *  App.Ui.AjaxIndicator
 *  App.Ui.AjaxRequest
 *  App.Ui.AjaxPanel
 *  App.Ui.ShadeLayer
 *  App.Ui.ModalLayer (TODO: remix, or obsolete?)
 *  
 * @uses    yahoo-dom-event.js
 * @author  Fabrice Denis
 */

/**
 * Augment YUI 2.7.0 with useful method/helpers,
 * some methods are "patched" from YUI 3.0.0 Beta1.
 * 
 * YAHOO.util.Dom.setStyles(element, properties)
 * YAHOO.bind(fn, scope [, args, ...] )
 * YAHOO.Array.each(arr, fn [, context])
 */
(function() {

  var Y = YAHOO,
      Dom = Y.util.Dom,
      ArrayNative = Array.prototype;
  
  Dom.setStyles = function(el, styles) {
    for (var s in styles) {
      Dom.setStyle(el, s, styles[s]);
    }
  };

  if (!Y.bind) {

    /**
     * Returns a function that will execute the supplied function in the
     * supplied object's context, optionally adding any additional
     * supplied parameters to the end of the arguments the function
     * is executed with.
     *
     * In some cases it is preferable to have the additional arguments
     * applied to the beginning of the function signature.  For instance,
     * FireFox setTimeout/setInterval supplies a parameter that other
     * browsers do not.  
     * Note: YUI provides a later() function which wraps setTimeout/setInterval,
     * providing context adjustment and parameter addition.  This can be 
     * used instead of setTimeout/setInterval, avoiding the arguments
     * collection issue when using bind() in FireFox.
     *
     * @param f {Function} the function to bind
     * @param c the execution context
     * @param args* 0..n arguments to append to the end of arguments collection
     * 
     * @return {function} the wrapped function
     */
    Y.bind = function(f, c) {
      var a = ArrayNative.slice.call(arguments, 2);
      return function () {
        return f.apply(c || f, ArrayNative.slice.call(arguments, 0).concat(a));
      };
    };
  }

  if (!Y.Array) {
  
    Y.Array = {};
    
    /**
      * Executes the supplied function on each item in the array.
      * @method each
      * @param a {Array} the array to iterate
      * @param f {Function} the function to execute on each item
      * @param o Optional context object
      * @static
      * @return {YUI} the YUI instance
      */
    Y.Array.each = function(a, f, o) {

      var l = (a && a.length) || 0, i;
      for (i = 0; i < l; i=i+1) {
        f.call(o || Y, a[i], i, a);
      }
      
      return Y;
    };
  }

})();


// global reference 
YAHOO.namespace('RevTK');

var App = YAHOO.RevTK;


/**
 * App.Toolkit helpers
 * 
 * App.Toolkit.toQueryString(obj)
 * 
 * @author fde
 */
(function() {

  var Y = YAHOO;
  
  App.Toolkit = {
    
    /**
     * Turns an object into its URL-encoded query string representation.
     *
     * @param {Object} obj   Parameters as properties and values 
     */
    toQueryString: function(obj, name) {
  
      var i, l, s = [];
  
      if (Y.lang.isNull(obj) || Y.lang.isUndefined(obj)) {
        return name ? encodeURIComponent(name) + '=' : '';
      }
      
      if (Y.lang.isBoolean(obj)) {
        obj = obj ? 1 : 0;
      }
      
      if (Y.lang.isNumber(obj) || Y.lang.isString(obj)) {
        return encodeURIComponent(name) + '=' + encodeURIComponent(obj);
      }
      
      if (Y.lang.isArray(obj)) {
        name = name; // + '[]'; don't do this for Java (php thing)
        for (i = 0, l = obj.length; i < l; i ++) {
          s.push(arguments.callee(obj[i], name));
        }
        return s.join('&');
      }
      
      // now we know it's an object.
      var begin = name ? name + '[' : '',
          end = name ? ']' : '';
      for (i in obj) {
        if (obj.hasOwnProperty(i)) {
          s.push(arguments.callee(obj[i], begin + i + end));
        }
      }
  
      return s.join("&");
    }
  };
})();


/**
 * A simple logging mechanism that works in Firefox and IE6+,
 * message logging maps to Firebug's Console API if it is present.
 * 
 * @author  Fabrice Denis
 */
(function(){

  var lineNr = 0,
      div = false;

  /**
   * Dynamically add the logger output div to the page.
   * 
   */
  function build()
  {
    var top, hd, bd;
    
    top = document.createElement('div');
    top.appendChild((hd = document.createElement('div')));
    top.appendChild((bd = document.createElement('div')));
    
    top.style.position = 'absolute';
    top.style.right = '0';
    top.style.top = '0';
    top.style.padding = '2px';
    top.style.background = '#ff4040';
    top.style.color = '#fff';
    top.style.font = '11px/13px "Courier New", monospace';
    top.style.width = '300px';
    
    hd.style.padding = '0 0 2px';
    
    bd.style.background = '#ffe0e0';
    bd.style.color = '#000';
    bd.style.width = '300px';
    bd.style.height = '200px';
    bd.style.overflow = 'scroll';

    hd.innerHTML = "Javascript Debug Log (click to toggle)";
    hd.onclick = function() {
      bd.style.display = (bd.style.display === 'none') ? 'block' : 'none';
      return false;
    };
    
    document.getElementsByTagName('body')[0].appendChild(top);
    
    return bd;
  }

  /**
   * Log a message to the console, or the dynamically created div.
   * 
   * Accepts sprintf() style arguments, see FireBug console API.
   * 
   * @see    http://getfirebug.com/console.html
   * 
   * @param  {String}  message
   */
  function log()
  {
    lineNr++;
  
    // create a div to output messages
    if (!div) {
      div = build();
    }
    
    var t = document.createTextNode('' + lineNr + ': ' + arguments[0]);
    var br = document.createElement('br');
    div.appendChild(t);
    div.appendChild(br);

    // scroll down
    if (typeof(div.scrollTop) !== "undefined") {
      div.scrollTop = div.scrollHeight;
    }
  }
  
  if (typeof(App) === "undefined") {
    App = {};
  }
  
  // map to Firebug's console when available (ignore Safari's console.log) 
  if (typeof(window.loadFirebugConsole) !== "undefined") {
    App.log = function()
    {
      console.log.apply(console, arguments);
    };
  }
  else {
    App.log = log;
  }

})();


if (!App.Ui) {
  App.Ui = {};
}


/**
 * EventDispatcher implements the observer design pattern.
 * 
 * Methods:
 *   connect(name, fn [, scope])
 *   disconnect(name[, fn])
 *   notify(name [, arg1[, ...]])
 *   hasListeners(name)
 * 
 * @see     http://developer.apple.com/documentation/Cocoa/Conceptual/Notifications/index.html Apple's Cocoa framework
 * 
 * @author  Fabrice Denis
 * @version 2009.07.17 (notify() now has return value, added scope to listeners)
 * @requires YAHOO
 */
(function (){

  App.Ui.EventDispatcher = function() {
    this.init.apply(this, arguments);
  };

  var
    Y = YAHOO,
    EventDispatcher = App.Ui.EventDispatcher;

  EventDispatcher.prototype = {

    listeners: null,

    init:function()
    {
      this.listeners = {};
    },
    
    destroy:function()
    {
      this.listeners = {};
    },
  
    /**
     * Connects a listener to a given event name.
     *
     * @param {String}    name     The type of event (the event's name)
     * @param {Function}  fn       A javascript callable
     * @param {Object}    context  Context (this) for the event. Default value: the window object.
     */
    connect:function(name, fn, context)
    {
      if (!this.listeners[name]) {
        this.listeners[name] = [];
      }

      this.listeners[name].push({
        fn:    fn,
        scope: context || window
      });
    },
    
    /**
     * Disconnects a listener, or all listeners, for an event.
     *
     * If fn is not specified, then all listeners for this event are unsubscribed.
     *
     * @param {String}    nalme   An event name
     * @param {Function}  fn      A javascript callable (OPTIONAL)
     *
     * @return {int}    Number of listeners unsubscribed
     */
    disconnect:function(name, fn)
    {
      var i, l, callables, s;
  
      if (!this.listeners[name]) {
        return false;
      }
  
      // if listener is undefined, delete all listeners
      if (!fn) {
        fn = true;
      }
  
      callables = this.listeners[name];
      l = callables.length;
      
      for (i = l - 1; i > -1; i--) {
        s = callables[i];
        if (true === fn || s.fn === fn) {
          delete s.fn;
          delete s.scope; 
          callables.splice(i, 1); // unset array item
        }
      }
 
      return l;
    },
  
    /**
     * Notifies all listeners of a given event.
     *
     * @param {String}  name       An event name
     * @param {Object*} arguments  An arbitrary set of parameters to pass to 
     *                             the handler.
     * 
     * @return {boolean} false if one of the subscribers returned false, 
     *                   true otherwise
     */
    notify:function()
    {
      var args = Array.prototype.slice.call(arguments, 0),
          name = args.shift(),
          callables = this.listeners[name] ? this.listeners[name] : [],
          i, ret, subscriber;

      if (args.length===1 && Y.lang.isArray(args[0])) {
        alert('EventDispatcher()  using obsolete notify() signature?');
      }
  
      if (!callables.length) {
        return;
      }
  
      for (i = 0; i < callables.length; i++) {
        subscriber = callables[i]; 
        ret = subscriber.fn.apply(subscriber.scope, args.length ? args : []);
        if (false === ret) {
          break;
        }
      }
      
      return (ret !== false);
    },
  
    /**
     * Returns true if the given event name has some listeners.
     *
     * @param String  sName    An event name
     *
     * @return Boolean true if some listeners are connected, false otherwise
     */
    hasListeners:function(name)
    {
      if (!this.listeners[name]) {
        return false;
      }
      return this.listeners[name].length > 0;
    }
  };
})();


/**
 * EventCache keeps track of events and allows to clear them all at once
 * when the object is destroyed.
 * 
 * Uses:
 * - Clearing events fixes a memory leak in old versions of IE.
 * - It is useful for ajax components, by clearing the events the content
 *   can be rendered more or less disabled until it is replaced with the result
 *   of an ajax call.
 * 
 * Methods:
 *   init(sDebug)
 *   addEvent(element, sEventType, fnEventHandler);
 *   destroy()
 *   
 * Examples:
 *   this.evtCache = new uiEventCache();
 *   this.evtCache.addEvent(elem, 'click', this.clickEvent.bindAsEventListener(this));
 * 
 */
(function(){

  App.Ui.EventCache = function() {
    this.init.apply(this, arguments);
  };

  var
    Event = YAHOO.util.Event,
    EventCache = App.Ui.EventCache;

  EventCache.prototype = {

    sId: null,
    
    eCache: null,

    init:function(sId)
    {
      this.sId = sId || '';
      this.eCache = [];
    },
    
    addEvent:function(element, sEventType, fn)
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
    addEvents:function(element, aEventTypes, fn)
    {
      var i;
      for (i = 0; i < aEventTypes.length; i++)
      {
        this.addEvent(element, aEventTypes[i], fn);
      }
    },
  
    push:function(element, sEventType, handler) {
      this.eCache.push({oElem:element, evType:sEventType, fn:handler});
    },
  
    destroy:function()
    {
      if (this.eCache)
      {
      // uiConsole.log('uiEventCache.destroy('+this.sId+') '+this.eCache.length+' events');
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


/**
 * Display a loading indicator in the top left corner of the container element.
 * 
 * Options
 *   container  {String|HTMLElement}  Parent element onto which the loading indicator is aligned.
 *                                    If not set, the indicator appears at the top right of the page.
 *   message    {String}              (Optional) Message to show in place of DEFAULT_MESSAGE, can contain html (eg. links)
 * 
 */
(function() {

  App.Ui.AjaxIndicator = function() {
    this.init.apply(this, arguments);
  };

  var Y = YAHOO,
      Dom = Y.util.Dom,
      AjaxIndicator = App.Ui.AjaxIndicator,
      // constants
      DEFAULT_ZINDEX = 100,
      DEFAULT_MESSAGE = 'Loading...';

  AjaxIndicator.prototype = {

    init:function(options)
    {
      this.container = options && options.container ? Dom.get(options.container) : document.body;
      this.message = options.message ? options.message : DEFAULT_MESSAGE;
      this.indicator = null;
    },
    
    destroy:function()
    {
      // remove from DOM and clear reference
      if (this.indicator && this.indicator.parentNode) {
        document.body.removeChild(this.indicator);
      }
      this.indicator = null;
    },
  
    show:function()
    {
      // create the element
      if (!this.indicator) {
        
        var pos = Dom.getXY(this.container);
  
        this.indicator = document.createElement('span');
        Dom.setStyles(this.indicator, {
          padding:    '2px 10px',
          background: 'red',
          color:      '#fff',
          font:       '13px/18px Arial, sans-serif',
          position:   'absolute',
          left:       pos[0] + 'px',
          top:        pos[1] + 'px',
          zIndex:     DEFAULT_ZINDEX,
          display:    'block'
        });
        this.indicator.innerHTML = this.message;
        document.body.insertBefore(this.indicator, document.body.firstChild);
      }
  
      this.indicator.style.display = 'block';
    },
    
    hide:function()
    {
      if (this.indicator) {
        this.indicator.style.display = 'none';
      }
    },
    
    /**
     * Return the html element used by the ajax indicator.
     * 
     * @return HTMLElement   Html element or null
     */
    getElement:function()
    {
      return this.indicator;
    }
  };
})();


/**
 * AjaxRequest is a wrapper for YUI's Connection manager.
 * 
 * Wrapper will allow to add global ajax loading indicator, request timeout handling, etc.
 * 
 * Also allows to pass parameters directly as an object (properties and values),
 * these parameters can be in addition to serializing of a form present on the page.
 * 
 * Constructor options:
 * 
 *   method      The request method ('GET', 'POST', ...), defaults to 'GET'
 *   parameters  Optional request parameters querystring (String) or hash (Object)
 *   form        If set (HTMLElement form|String id), the form is serialized in addition to the parameters
 *   nocache     If specified (true), a 'rnd=timestamp' variable is added to the request to prevent caching
 *   events      Custom event handlers for this transaction (see YAHOO.Connect "customevents")
 *    onSuccess   
 *    onFailure
 *    onAbort
 *    onStart
 *    onComplete
 *   scope       Scope for the event handlers (optional)
 *   argument    Object, string, number, or array that will be passed to your callbacks (optional)
 *   timeout     Timeout value in milliseconds (defaults to 3000)
 *   
 * The success, failure and upload handlers will receive YUI's Response Object:
 * 
 *   tId
 *   status
 *   statusText
 *   getResponseHeader[]
 *   getAllResponseHeaders
 *   responseText
 *   responseXML
 *   argument
 * 
 * @link   http://developer.yahoo.com/yui/connection/
 * 
 */
(function() {

  /**
   * Constructor.
   * 
   * @param {String}              Request url, if it contains a query string, don't set options.parameters
   * @param {Object} (Optional)   Options
   */
  App.Ui.AjaxRequest = function(url, options)
  {
    this.init.apply(this, arguments);
  };
  
  // internal shorthands
  var 
    Y = YAHOO,
    Lang = Y.util.Lang,
    Dom = Y.util.Dom,
    Event = Y.util.Event,
    Connect = Y.util.Connect,
    AjaxRequest = App.Ui.AjaxRequest,
    
    // constants
    DEFAULT_TIMEOUT = 3000;


  AjaxRequest.prototype = {
    
    init:function(url, options)
    {
      var callback = {},
          postdata;
    
      App.log('Ui.AjaxRequest.init()');
      
      // set defaults
      options = Y.lang.merge({
        method:    'GET',
        url:       url
      }, options || {});

      options.method = options.method.toUpperCase();

      // build YUI's Callback object
      if (options.success) {
        callback.success = options.success;
      }

      if (options.failure) {
        callback.failure = options.failure;
      }
      
      if (options.argument) {
        callback.argument = options.argument;
      }

      if (options.nocache) {
        callback.cache = false;
      }

      if (options.events) {
        callback.customevents = options.events;
      }
      
      if (options.scope) {
        callback.scope = options.scope;
      }
      
      callback.timeout = Y.lang.isNumber(options.timeout) ? options.timeout : DEFAULT_TIMEOUT; 
      
      // serialize form data?
      if (options.form) {

        var formObject = Dom.get(options.form);
          
        if (!formObject.nodeName || formObject.nodeName.toLowerCase()!=='form') {
          App.halt("AjaxRequest() 'form' is not a FORM element");
        }

        Y.util.Connect.setForm(formObject);
      }
      
      // create the request URL
      var requestUri = options.url,
          params = options.parameters;

    // convert request parameters to url encoded string (damn you, YUI)
      if (params) {

        if (Y.lang.isString(params)) {
          var pos = params.indexOf("?");
          if (pos >= 0) {
            params = params.slice(pos + 1);
          }
        }
        else if (Y.lang.isObject(params)) {
          // convert hash to query string parameters
          params = App.Toolkit.toQueryString(params);
        }
        else {
          App.halt("AjaxRequest() invalid typeof options.parameters");
        }

        // add GET request query string
        if (options.method === 'GET') {
          // should not query string in url AND and options.parameters at the same time 
          if (requestUri.indexOf("?") >= 0) {
            App.halt("AjaxRequest() Request url already contains parameters");
          }
          requestUri = requestUri + "?" + params;
        }
        else if (options.method === 'POST') {
          // handle extra POST data
          if (requestUri.indexOf("?") >= 0) {
            App.halt("AjaxRequest() POST request url contains query string (unsupported)");
          }
          // set the YUI parameter for post data in query string format
          postdata = params;
        }
      }

      this.connection = Y.util.Connect.asyncRequest(options.method, requestUri, callback, postdata); 
    }

  };
  
})();


/**
 * Ui.AjaxPanel is a wrapper for all ajax requests in the application.
 * 
 * Featutes:
 * - centralized support for handling errors, timeouts and HTTP redirects
 * - handles communication of content between client and server for a
 *   portion of a html page (text/html), or JSON data (application/json).
 * 
 * Notes:
 * 
 * - During the Ajax communication, the portion of the page is covered with a
 *   layer that blocks mouse clicks. By default it is not visible (fully transparent),
 *   but can be set to shading with option 'bUseShading'.
 * 
 * - Content cant be sent as a typical HTTP request or as JSON data.
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
 *     initContent      By default, the onContentInit notification is fired when the AjaxPanel is instanced,
 *                      set to false to skip the first onContentInit (notify only after subsequent ajax requests)           
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
 *                                   to proceed with the default form submission.
 * 
 * Methods:
 *   setForm(form)                   Set form to serialize with the next get/post/send() request (string id | HTMLElement)
 *   get(parameters)                 Do a GET request, accepts additional parameters in hash or query string format
 *   post(parameters)                Do a POST request, accepts additional parameters in hash or query string format
 *   send(parameters)                Do either a GET or POST depending on the form's method attribute, accepts additional parameters
 *                                   This method requires a form present in the panel!
 *   connect()                       Call after a failed request (get, post, send) to retry the last request
 * 
 * @version  2009.07.17 (uses new EventDispatcher, onSubmitForm return value)
 * @author   Fabrice Denis
 */
(function() {
 
  App.Ui.AjaxPanel = function(el, config)
  {
    this.init.apply(this, arguments);
  };
  
  // internal shorthands
  var 
    Y = YAHOO,
    Dom = Y.util.Dom,
    Event = Y.util.Event,
    AjaxPanel = App.Ui.AjaxPanel;
    
  AjaxPanel.prototype = {

    // Custom Events instances
    events: {},
    
    // Form to seriliaze with next get() or post() call
    serializeForm: false,

    // Set true after at least one succesful html content request 
    contentLoaded: false,
    
    // AjaxPanel can be "plugged in" with global pre-processing for the ajax responses
    // by replacing the following methods
    responseFilter: {
      onSuccess: function() { return true; },
      onFailure: function() { return true; }
    },

    init:function(container, options)
    {
      App.log('AjaxPanel.init()');
  
      options = !!options ? options : {};
  
      // set defaults
      this.options = options;
      this.options.form = (options.form && options.form !== true) ? options.form : true;
      this.options.bUseLayer = options.bUseLayer || true;
      this.options.bUseShading = options.bUseShading || false;
      this.options.initContent = options.initContent === false ? false : true;
  
      this.container = Dom.get(container);
      if (!this.container) {
        App.halt("AjaxPanel.init() invalid container");
      }
  
      this.evtCache = new App.Ui.EventCache();
  
      this.ajaxRequest = null;
  
      // register events
      this.eventDispatcher = new App.Ui.EventDispatcher();
      if (this.options.events) {
        var events = this.options.events, eventName;
        for (eventName in events) {
console.log('poo'+eventName);
          this.eventDispatcher.connect(eventName, events[eventName]);
        }
      }
  
      this.initContent();
    },
    
    destroy:function()
    {
      if (this.shadeLayer) {
        this.shadeLayer.destroy();
      }
      this.evtCache.destroy();
      this.eventDispatcher.destroy();
    },
  
    initContent:function()
    {
    //App.log('AjaxPanel.initContent()');
      
      // Attach an event to FORMs that will dispatch a "onSubmit" event.
      var elForm = this.getForm();
      if (elForm) {
        this.evtCache.addEvent(elForm, 'submit', Y.bind(this.submitFormEvent, this));
      }

      if (this.contentLoaded || this.options.initContent === true) {
        this.eventDispatcher.notify('onContentInit');
      }
    },
  
    replaceContent:function(sHtml)
    {
    //  App.log('AjaxPanel.replaceContent()');
  
      this.evtCache.destroy();
      
      this.eventDispatcher.notify('onContentDestroy');
      
      this.container.innerHTML = sHtml;
  
      // setup UI for forms, tables, etc.
      this.initContent();
    },
  
    /**
     * Sets form to use with the next request (serialize data & action attribute).
     * 
     * @param  {String|HTMLElement}  elForm   Form id or element
     */
    setForm:function(elForm)
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
    getForm:function()
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
          App.halt("AjaxPanel.getForm() form not found (by class name)");
        }
        return form;
      }
      
      return this.options.form;
    },
  
    /**
     * 
     * @param {Object} oEvent
     */
    submitFormEvent:function(e)
    {
      var form, skipSubmit = false;
      
      App.log('AjaxPanel.submitFormEvent(%o) Form %o', e, Event.getTarget(e));

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
    get:function(oData)
    {
      this.prepareConnect(oData, 'get');
    },
  
    /**
     * Do a POST request with optional parameters to add to the request.
     * 
     * @param {Object} oData   A hash with variables, or a query string, or undefined (optional)
     */
    post:function(oData)
    {
      this.prepareConnect(oData, 'post');
    },

    /**
     * Do a GET or POST request, using the active form's "method" attribute. 
     * 
     * @param {Object} oData   A hash with variables, or a query string, or undefined (optional) 
     */
    send:function(oData)
    {
      var form = this.getForm() || App.halt("AjaxPanel.send()  Requires a form");
      var method = form.getAttribute('method') || 'post';
      this.prepareConnect(oData, method);
    },
    
    /**
     * 
     * @param {Object} oData    A hash with post variables, or a query string, or undefined
     * @param {string} sMethod  Method name 'post' or 'get'
     */
    prepareConnect:function(oData, sMethod)
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
        App.halt('AjaxPanel.post() need post_url and/or a FORM element');
      }
      
      // dont send multiple requests at the same time
      if (this.ajaxRequest) {
        alert('Not so fast!');
        return;
      }
  
    //  App.log('AjaxPanel.prepareConnect(%o, %s) FORM %o', oData, sMethod, form);

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
    connect:function(oConnect)
    {
      if (oConnect) {
        this.connection = oConnect;
      }
  
      if (!this.connection) {
        App.halt('AjaxPanel.connect() No connection object.');
      }
  
      var options = {
        method:       this.connection.method,
        form:         this.connection.form,
        parameters:   this.connection.parameters,
        
        nocache:      true,

        events: {
          onSuccess:  this.ajaxOnSuccess,
          onFailure:  this.ajaxOnFailure,
          // ajax indicator
          onStart:    this.ajaxOnStart,
          onComplete: this.ajaxOnComplete
        },
        scope:        this

      };

      this.ajaxRequest = new App.Ui.AjaxRequest(this.connection.url, options);
    },
  
    ajaxOnStart:function(eventType, args)
    {
      App.log('AjaxPanel.ajaxOnStart(%o)', args);
      
      // layer
      if (this.options.bUseLayer) {
        // create layer, then reuse it
        if (!this.shadeLayer) {
          this.shadeLayer = new App.Ui.ShadeLayer({
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
      this.ajaxIndicator = new App.Ui.AjaxIndicator({container: this.container});
      this.ajaxIndicator.show();
    },
    
    ajaxOnComplete:function(eventType, args)
    {
      App.log('AjaxPanel.ajaxOnComplete(%o)', args);

      //  App.log('AjaxPanel.ajaxOnComplete()');
      //var response = args[0];
      
      // hide loading indicator
      this.ajaxIndicator.destroy();
  
      if (this.shadeLayer) {
        this.shadeLayer.hide();
      }
    },
    
    ajaxOnSuccess:function(eventType, args)
    {
      App.log('AjaxPanel.ajaxOnSuccess(%o)', args);

      var oAjaxResponse = args[0];

      if (this.responseFilter.onSuccess(oAjaxResponse)) {
        this.eventDispatcher.notify('onResponse', oAjaxResponse);

        if (oAjaxResponse.getResponseHeader['Content-Type'].indexOf('text/html') === 0 &&
          oAjaxResponse.responseText.length)
        {
          this.contentLoaded = true;
          this.replaceContent(oAjaxResponse.responseText);
        }
      }
  
      this.ajaxRequest = null;
    },
    
    ajaxOnFailure:function(eventType, args)
    {
      App.log('AjaxPanel.ajaxOnFailure(%o)', args);
    
      var oAjaxResponse = args[0];

      if (this.responseFilter.onFailure(oAjaxResponse)) {

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
      }
      
      this.ajaxRequest = null;
    },
    
    /**
     * Display a message in place of the ajax indicator,
     * with a "Retry" link.
     * 
     * @param {Object} sMessage
     */
    showErrorMessage:function(sMessage)
    {
      this.ajaxErrorIndicator = new App.Ui.AjaxIndicator({
        container: this.container, 
        message: sMessage + ' <a href="#" style="font-weight:bold;color:yellow;">Retry</a>'
      });
      this.ajaxErrorIndicator.show();
      
      var elMessage = this.ajaxErrorIndicator.getElement();
      var elRetryLink = elMessage.getElementsByTagName('a')[0];
      
      var retry = function(oEvent) {
        App.log('AjaxPanel.ajaxRetryEvent()');
        this.ajaxErrorIndicator.destroy();
        this.connect();
      };

      Event.on(elRetryLink, 'click', retry, this, true);
    }
  };

})();


/**
 * ShadeLayer creates a absolutely positioned div that covers an area of the page,
 * with a solid color and a level of transparency. Typical use is to show an
 * area as "disabled" while a dialog is on, or while content is loading with ajax.
 * 
 * Options
 *   element                   If set, the layer is positioned to cover the element's area.
 *   pos
 *     left, top               Must be set if element is not specified.
 *   size
 *     width, height           Must be set if element is not specified. 
 *   color                     The solid color to use, in CSS format ('#rgb' or '#rrggbb'). (OPTIONAL)
 *   opacity                   Should be a value from 0 to 100. 0 is invisible, 100 is opaque. (OPTIONAL)
 *   glass_mode                The layer is fully transparent (no color, no opacity) (DEFAULT false)
 * 
 * Methods
 * 
 *   show()
 *   hide()
 *   resize()           Updates the layer dimensions based on the element (element option must be set)
 *                      This is mostly for when the element is the document body, and the window is resized.
 *   visible()          Returns true if visible
 *   getLayer()         Returns the DIV element created for the shade layer
 *   getDimensions()    Returns dimensions of the element, if element is document.body returns viewport dimensions.
 * 
 * Usage
 * 
 *   When the layer is created, it is not shown by default, show() must be called.
 *   This allows to change some default values such as zIndex, before displaying the layer.
 * 
 */
(function(){
  
  App.Ui.ShadeLayer = function() {
    this.init.apply(this, arguments);
  };

  var
    Y = YAHOO,
    Dom = Y.util.Dom,
    Event = Y.util.Event,
    ShadeLayer = App.Ui.ShadeLayer,

    /**
     * Default settings
     */
    DEFAULT_COLOR = '#000',
    DEFAULT_OPACITY = 20,
  
    /**
     * Currently assumed to be lower value than App.Ui.AjaxIndicator DEFAULT_ZINDEX
     */
    DEFAULT_ZINDEX = 90;

  ShadeLayer.prototype = {
  
    init:function(options)
    {
      this.color = options.color || DEFAULT_COLOR;
  
      this.opacity = typeof(options.opacity)!=='undefined' ? options.opacity : DEFAULT_OPACITY;
      this.opacity = Math.max(Math.min(this.opacity, 100), 0);
  
      // look for conditional comment div
      this.isIE = !!Dom.get('ie');
  
      if (options.element)
      {
        var offsets = Dom.getXY(options.element);
        this.pos  = {
          left: offsets[0],
          top:  offsets[1]
        };
        
        this.size = this.getDimensions(options.element);

        this.element = options.element;
      }
      else
      {
        this.pos  = options.pos;
        this.size = options.size;
      }

      var elLayer = document.createElement("div");
      var layerStyles = {
        display:  'none',
        position: 'absolute',
        top:      this.pos.top+'px',
        left:     this.pos.left+'px',
        zIndex:   DEFAULT_ZINDEX,
         width:    /%$/.test(this.size.width) ? this.size.width : this.size.width+'px',
        height:   /%$/.test(this.size.height) ? this.size.height : this.size.height+'px'
      };
      
      // set shading
      if (!options.glass_mode) {
          // ex: background:#85a5d2; opacity:0.5; filter:alpha(opacity=50);
        layerStyles.background = this.color;
         layerStyles.opacity = Math.round(this.opacity)/100;
      }
  
      Dom.setStyles(elLayer, layerStyles);
       if (this.isIE){
         // IE6 opacity filter
        elLayer.style.filter = 'alpha(opacity=' + this.opacity + ')';
      }

  //console.log('x %o', elLayer);

      this.elBody = document.getElementsByTagName("body")[0];
      this.elBody.insertBefore(elLayer, this.elBody.firstChild);
      this.elLayer = elLayer;
    },
    
    show:function()
    {
      // update dimensions (if repeating show/hide and the container content changes)
      if (this.element) {
        this.resize();
      }
      Dom.setStyle(this.elLayer, "display", "block");
    },
    
    hide:function()
    {
      Dom.setStyle(this.elLayer, "display", "none");
    },
  
    resize:function()
    {
      if (!this.element) {
        throw new Error('uiShadeLayer.resize() cannot resize without element');
      }
      
      var newSize = this.getDimensions(this.element);
  
      // try to fix small gap caused by Firefox
  //    var elementSize = 
  
      if (newSize.width != this.size.width || newSize.height != this.size.height) 
      {
        this.size = newSize;
        //  uiConsole.log('resize to %o', this.size);
        Dom.setStyles(this.elLayer,
        {
          width: this.size.width + 'px',
          height: this.size.height + 'px'
        });
      }
    },
    
    visible:function()
    {
      return this.elLayer.style.display !== "none";
    },
  
    destroy:function()
    {
      if (this.elLayer) {
        var elBody = document.getElementsByTagName("body")[0];
        this.elBody.removeChild(this.elLayer);
      }
      this.elLayer = null;
    },
    
    getLayer:function()
    {
      return this.elLayer;
    },
  
    /**
     * Wrapper that returns the dimensions of the entire viewport if element is the
     * document body, or the dimensions of a specific element, calls the appropriate
     * Prototype function.
     * 
     * @param {HTMLElement}  element
     */
    getDimensions:function(element)
    {
      if (element === document.body)
      {
        return {
          width: Dom.getViewportWidth(),
          height: Dom.getViewportHeight() 
        };
      }
      else
      {
        var region = Dom.getRegion(element);
        return {
          width: region.width,
          height: region.height
        };
      }
    }
  };

})();


/**
 * uiModalLayer takes any DIV and makes it into a centered "popup" layer,
 * with a shading of the background that blocks clicks until the layer is closed.
 * 
 * Options
 *   element       The div to center and show as the "popup"
 *   use_shading   Defaults to true, if false, the background layer is fully transparent
 * 
 * IE6 Select Display bug:
 * 
 *   For IE6, the conditional comment DIV id "ie" is required.
 *   IF the div is present, the following fix is applied: the class "IE6ComboBoxFix" is
 *   added to SELECTs while the popu player is shown. This class should be like this:
 *   .IE6ComboBoxFix { visiblity:hidden; }
 * 
 * @requires  yahoo, dom, event, container
 */
(function() {

  /**
   * It does something, yo.
   * 
   * @param {String} s  Name of the looney
   * @return {Boolean}
   */
  App.Ui.ModalLayer = function() {
    this.init.apply(this, arguments);
  };
  
  var Y = YAHOO,
      Dom = Y.util.Dom,
      Event = Y.util.Event,
      ModalLayer = App.Ui.ModalLayer; 

  ModalLayer.prototype = {
    
    oPanel: null,
    
    init:function(options)
    {
      // set defaults
      options.use_shading = !(options.use_shading===false);

      var elPanel = Dom.get(options.element);

      this.oPanel = new Y.widget.Panel(elPanel, {
        fixedcenter: true,
        constraintoviewport: true,
        underlay: options.use_shading ? "shadow" : "none",
        modal: true,
        draggable: false
      });

      this.oPanel.render();
      this.oPanel.show();
    },
    
    destroy:function()
    {
      this.oPanel.hide();
      this.oPanel.destroy();
    }
  };
   
})();

