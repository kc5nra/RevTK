/*jslint forin: true */
/*global window, Core, App, YAHOO, alert, console, document */
/**
 * AjaxRequest is a wrapper for YUI's Connection manager.
 * 
 * Features:
 * 
 * - Allows to pass parameters directly as an object,
 *   if the form serializing is used, these parameters will be merged to the form data.
 * - Augments the response object with a "responseJSON" property (requires content-type "application/json")
 * - Allows sending JSON data (as a JSON string), from a native javascript object 
 * 
 * Sending JSON data (requires YAHOO.lang.JSON):
 * 
 *   If the parameters hash contains a "json" property, its contents will be encoded
 *   into a JSON string (eg: parameters: { json: { mydata: "lorem ipsum" } } ).
 *   
 * Receiving JSON data (requires YAHOO.lang.JSON):
 * 
 *   success_handler: function(o) {
 *     if (o.responseJSON) {
 *       Core.log("Received JSON data: %o", o.responseJSON);
 *     }
 *     else if (o.responseJSON === null) {
 *       Core.log("JSON data could not be parsed");
 *     }
 *     else {
 *       Core.log("No JSON data response, or not application/json");
 *     }
 *   } 
 * 
 * 
 * Constructor options:
 * 
 *   method      The request method ('GET', 'POST', ...), defaults to 'GET'
 *   parameters  Optional request parameters as a query string (String) or hash (Object)
 *               !!! A "json" parameter is automatically converted to JSON if it contains an object !!!
 *   form        If set (HTMLElement form|String id), the form is serialized in addition to the parameters
 *   nocache     If specified (true), a 'rnd=timestamp' variable is added to the request to prevent caching
 *   argument    Object, string, number, or array that will be passed to your callbacks (optional)
 *               Use o.argument to access this property in the handlers.
 *   timeout     Timeout value in milliseconds (defaults to 3000)
 * 
 *   success(o)  HTTP 2xx range response, o is the YUI Connect response object
 *   failure(o)  HTTP 400 or greater response, o is the YUI Connect response object
 *   upload(o)   Process file upload response (untested, maps to YUI Connection Manager option)
 *    
 *   scope       Scope for "success", "failure", and "events" handlers (optional)
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
 *   responseJSON   => an object corresponding to a "application/json" response ("success" handler only) 
 *   argument       => options.argument as passed to AjaxRequest constructor
 * 
 * Methods:
 * 
 *   isCallInProgress()   Determines if the transaction is still being processed.
 * 
 * @see    http://developer.yahoo.com/yui/connection/
 * 
 * @requires  2.8.0r4/build/connection/connection_core-min.js
 * @requires  2.8.0r4/build/json/json-min.js
 */
/* =require from "%YUI2%" */
/* =require "/connection/connection-min.js" */
/* =require "/json/json-min.js" */
(function() {

  /**
   * Constructor.
   * 
   * @param {String} url      Request url, if it contains a query string, don't set options.parameters
   * @param {Object} options  Constructor options
   */
  Core.Ui.AjaxRequest = Core.createClass();
  
  // internal shorthands
  var Y = Core.YUI,
      Dom = Y.util.Dom,
      AjaxRequest = Core.Ui.AjaxRequest,

      // constants
      DEFAULT_TIMEOUT = 20000; // default time out for AjaxRequests

  AjaxRequest.prototype =
  {
    /**
     * Plugin function references to do global processing of the ajax responses.
     * The function should return true to indicate that processing the response can proceed.
     * Note that AjaxDialog uses AjaxPanel, and AjaxPanel uses AjaxRequest!  
     * @type {Object}
     */
    responseFilter: {
      onSuccess: function() { return true; },
      onFailure: function() { return true; }
    },
      
    init: function(url, options)
    {
      var that = this,
          callback = {},
          postdata;
    
      Core.log('Core.Ui.AjaxRequest(%o)', options);
      
      // set defaults
      options = Y.lang.merge({
        method:    'GET',
        url:       url
      }, options || {});

      options.method = options.method.toUpperCase();

      callback.success = function(o){ return that.handleSuccess(o, options.success, options.scope); };
      callback.failure = function(o){ return that.handleFailure(o, options.failure, options.scope); };

      if (options.upload) {
        callback.upload = options.upload;
      }
      
      if (options.argument) {
        callback.argument = options.argument;
      }

      if (options.nocache) {
        callback.cache = false;
      }

      // this should only be used internally from now (AjaxPanel)
      if (options.events) {
        if (options.events.onSuccess || options.events.onFailure) {
          Core.warn("AjaxRequest() WARNING: options.events is deprecated! (for internal use)");
        }
        callback.events = options.events;
      }
      
      if (options.scope) {
        callback.scope = options.scope;
      }
      
      callback.timeout = Y.lang.isNumber(options.timeout) ? options.timeout : DEFAULT_TIMEOUT; 
      
      // serialize form data?
      if (options.form) {

        var formObject = Dom.get(options.form);
          
        if (!formObject.nodeName || formObject.nodeName.toLowerCase()!=='form') {
          Core.error("AjaxRequest() 'form' is not a FORM element");
        }

        Y.util.Connect.setForm(formObject);
      }
      
      // create the request URL
      var requestUri = options.url,
          params = options.parameters;

      // convert request parameters to url encoded string (damn you, YUI)
      if (params)
      {
        if (Y.lang.isString(params))
        {
          var pos = params.indexOf("?");
          if (pos >= 0) {
            params = params.slice(pos + 1);
          }
        }
        else if (Y.lang.isObject(params))
        {
          // encode JSON data
          if (Y.lang.isObject(params.json)) {
            params.json = Y.lang.JSON.stringify(params.json);
          }
          
          // convert hash to query string parameters
          params = Core.Toolkit.toQueryString(params);
        }
        else {
          Core.error("AjaxRequest() invalid typeof options.parameters");
        }

        // add GET request query string
        if (options.method === 'GET')
        {
          // should not query string in url AND and options.parameters at the same time 
          if (requestUri.indexOf("?") >= 0) {
            Core.error("AjaxRequest() Request url already contains parameters");
          }
          requestUri = requestUri + "?" + params;
        }
        else if (options.method === 'POST')
        {
          // handle extra POST data
          if (requestUri.indexOf("?") >= 0) {
            Core.error("AjaxRequest() POST request url contains query string (unsupported)");
          }
          // set the YUI parameter for post data in query string format
          postdata = params;
        }
      }

      this.connection = Y.util.Connect.asyncRequest(options.method, requestUri, callback, postdata); 
    },
    
    /**
     * Determines if the transaction is still being processed.
     * 
     * @param {Object} o  The connection object returned by Y.util.Connect.asyncRequest
     * @return {Boolean}
     * 
     */
    isCallInProgress: function(o)
    {
      return Y.util.Connect.isCallInProgress(this.connection);
    },
    
    /**
     * The success handler is called for HTTP 2xx async responses.
     * 
     * Handles JSON responses, and responseFilter plugin.
     * 
     * Adds a "responseJSON" property to the YUI Connect object, if the content type
     * is "application/json" and the response is parsed succesfully.
     * 
     * If there IS an "application/json" response but it did not parse, responseJSON
     * is set to null instead of undefined. This lets you know that the JSON parse failed.
     * 
     * @param {Object} o      YUI Connect object
     * @param {Function=} fn   Success handler (optional)
     * @param {Object=} scope  Scope for the event handler (optional)  
     */
    handleSuccess: function(o, fn, scope)
    {
      if (this.responseFilter.onSuccess(o) && fn)
      {
        // set responseJSON
        if (o.responseText.length > 0 && o.getResponseHeader &&
            o.getResponseHeader['Content-Type'].indexOf('application/json') >= 0) 
        {
          try 
          {
            o.responseJSON = Y.lang.JSON.parse(o.responseText);
          } 
          catch (e) 
          {
            o.responseJSON = null;
          }
        }
        
        fn.apply(scope || window, [o]);
      }
    },
    
    /**
     * The failure method is called with HTTP status 400 or greater.
     * 
     * Handle the responseFilter plugin.
     * 
     * @param {Object} o
     * @param {Function=} fn   Failure handler (optional)
     * @param {Object=} scope
     */
    handleFailure: function(o, fn, scope)
    {
      if (this.responseFilter.onFailure(o) && fn)
      {
        fn.apply(scope || window, [o]);
      }
    }
  };
  
})();
