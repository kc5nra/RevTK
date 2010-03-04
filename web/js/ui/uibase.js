/*! uiBase Javascript Framework (c) Fabrice Denis - http://kanji.koohii.com */
/**
 * uiBase
 * 
 *  uiConsole
 *  uiEventDispatcher
 *  uiEventCache
 *  uiKeyboard
 *  uiAjaxRequest
 *  uiAjaxIndicator
 *  uiAjaxPanel
 *  uiShadeLayer
 *  uiModalLayer
 *  uiModalDialog
 *  uiPopupLayer
 *  
 * 
 * @uses    Prototype.js
 * @author  Fabrice Denis
 */

/**
 * Cross browser output of debug messages.
 * This class does NOT use a Javascript framework so it can be used on its own.
 * 
 * @author  Fabrice Denis
 */
var uiConsole =
{
	// Id of div for log messages in browsers without console
	DIV_ID: '_uiDebugConsole',
	
	enabled: false,
	debugCounter: 0,

	// Params: string message, plus optional parameters (cf. FireBug console.log)
	log:function()
	{
		// skip log messages
		if (!this.enabled)
			return;
	    
	    this.debugCounter++;
	
		// use FireBug console if present
		if (typeof(console)!=='undefined' && console.firebug)
		{
			console.log.apply(console, arguments);
			return;
		}
	
		var found = !!document.getElementById(this.DIV_ID);
		if (!found) {
			// create the debugging div
			d = document.createElement('div');
			d.id = this.DIV_ID;
			d.style.position = "absolute";
			d.style.right = '0';
			d.style.top = '0';
			d.style.border = "2px solid #dc0000";
			d.style.background = '#fff5f5';
			d.style.padding = '5px';
			d.style.textAlign = 'left';
			d.style.color = '#000';
			d.style.font = '11px Courier New, monospace;';
			document.getElementsByTagName('body')[0].appendChild(d);
		}
		var t = document.createTextNode(''+this.debugCounter + ': ' + arguments[0]);
	    var br = document.createElement('br');
		d.appendChild(t);
	  d.appendChild(br);
	}
}


/**
 * uiEventDispatcher implements the observer design pattern.
 * 
 * Methods:
 * 
 *   connect(sName, fnListener)
 *   disconnect(sName, fnListener)
 *   notify(sName, aArguments)
 *   hasListeners(sName)
 * 
 * @see http://developer.apple.com/documentation/Cocoa/Conceptual/Notifications/index.html Apple's Cocoa framework
 * 
 * @author  Fabrice Denis
 * @author  Based on sfEventDispatcher by Fabien Potencier (Symfony php framework)
 */
var uiEventDispatcher = Class.create();
uiEventDispatcher.prototype =
{
	initialize:function()
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
	 * @param String    sName       An event name
	 * @param Function  fnListener  A javascript callable
	 */
	connect:function(sName, fnListener)
	{
		if (!this.listeners[sName])
		{
			this.listeners[sName] = [];
		}
		
		this.listeners[sName].push(fnListener);
	},
	
	/**
	 * Disconnects a listener for a given event name.
	 *
	 * If fnListener is not specified, ALL listeners connected to the event
	 * are removed. This removes headaches with anonymous functions (eg. Prototype bind())
	 * for the simple scenarios where you know there is only one object tied to any particular event).
	 *
	 * @param String    sName       An event name
	 * @param Function  fnListener  A javascript callable (OPTIONAL)
	 *
	 * @return Boolean  false if listener does not exist, true otherwise
	 */
	disconnect:function(sName, fnListener)
	{
		var i;

		if (!this.listeners[sName]) {
			return false;
		}

		if (fnListener) {
			var i, callables = this.listeners[sName];
			for (i = 0; i < callables.length; i++) {
				if (callables[i] === fnListener) {
					callables.splice(i, 1); // unset array item
				}
			}
		} 
		else {
			delete this.listeners[sName];
		}
		
		return true;
	},

	/**
	 * Notifies all listeners of a given event.
	 *
	 * @param String  sName    An event name
	 */
	notify:function(sName, aArguments)
	{
		var i, callables = this.listeners[sName] ? this.listeners[sName] : [];

		for (i = 0; i < callables.length; i++) {
			callables[i].apply(this, aArguments ? aArguments : []);
		}
	},

	/**
	 * Returns true if the given event name has some listeners.
	 *
	 * @param String  sName    An event name
	 *
	 * @return Boolean true if some listeners are connected, false otherwise
	 */
	hasListeners:function(sName)
	{
		if (!this.listeners[sName]) {
			return false;
		}
		return this.listeners[sName].length > 0;
	}
}


/**
 * uiEventCache keeps track of events and allows to clear them all at once
 * when the object is destroyed.
 * 
 * Uses:
 * - Clearing events fixes a memory leak in old versions of IE.
 * - It is useful for ajax components, by clearing the events the content
 *   can be rendered more or less disabled until it is replaced with the result
 *   of an ajax call.
 * 
 * Methods:
 *   initialize(sDebug)
 *   addEvent(element, sEventType, fnEventHandler);
 *   destroy()
 *   
 * Examples:
 *   this.evtCache = new uiEventCache();
 *   this.evtCache.addEvent(elem, 'click', this.clickEvent.bindAsEventListener(this));
 * 
 */
var uiEventCache = Class.create();
uiEventCache.prototype =
{
	initialize:function(sId) {
		this.sId = sId || '';
		this.eCache = [];
	},

	addEvent:function(element, sEventType, fn)
	{
		Event.observe(element, sEventType, fn);
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
//			uiConsole.log('uiEventCache.destroy('+this.sId+') '+this.eCache.length+' events');
			for(var i=this.eCache.length-1; i>=0; i--) {
				var evc = this.eCache[i];
				Event.stopObserving(evc.oElem, evc.evType, evc.fn);
			}
			// free up references to the elements
			this.eCache = [];
		}
	}
}


/**
 * uiKeyboard adds simple keyboard shortcut handling with callbacks.
 * 
 * - Only alphanumerical characters.
 * - Control key combos do not trigger the callback so as not to override
 *   the default browser behaviour (eg: Ctrl-N for New Window).
 * - Only one listener for a key at one time.
 * 
 * Options:
 * 
 *   bDisableInInput      Defaults to true, do not call listener when key is pressed
 *                        while INPUT, TEXTAREA or SELECT is active.
 * 
 * Methods:
 * 
 *   addListener(sKey, fnListener)
 *   removeListener(sKey)
 *   destroy();
 * 
 * Usage:
 * 
 *   The listener callback receives the Prototype event object as argument.
 * 
 *   Use Prototype's bindAsEventListener to pass a closure to addListener(), and
 *   use the extra argument to identify the key, this is easier than checking the
 *   keycode from the event object.
 *   
 *   addListener('s', this.save.bindAsEventListener(this, 'save');
 * 
 */
var uiKeyboard = Class.create();
uiKeyboard.prototype =
{
	oKeys: null,
	
	initialize:function(options)
	{
		// set options and defaults
		options = options ? options : {};
		options.bDisableInInput = options.bDisableInInput!==false;
		this.options = options;

		this.oKeys = {};
		this.evtCache = new uiEventCache();
		this.evtCache.addEvent(document, 'keydown', this.evKeydown.bindAsEventListener(this));
	},
	
	destroy:function()
	{
		this.evtCache.destroy();
	},

	addListener:function(sKey, fnListener)
	{
		this.oKeys[sKey] = fnListener;
	},
	
	removeListener:function(fnListener)
	{
		this.oKeys[sKey] = null;
	},

	evKeydown:function(oEvent)
	{
	//	var iKeyCode = window.event ? event.keyCode : oEvent.keyCode;
	//	var sKeyChar = String.fromCharCode(iKeyCode).toLowerCase();

		// Don't enable shortcut keys in Input, Textarea fields
		if (this.options.bDisableInInput)
		{
			var element = oEvent.element();
			if (element.nodeType==3)
				element = element.parentNode;
			if (element.tagName == 'INPUT' ||
				element.tagName == 'TEXTAREA' ||
				element.tagName == 'SELECT')
			{
				return;
			}
		}

		var sKeyChar = String.fromCharCode(oEvent.keyCode).toLowerCase();
		var isCtrl = oEvent.ctrlKey;
		var bEatKey = false;
		var iKeyCode = sKeyChar.charCodeAt(0);  // get the lowercase letter key code

		if (!isCtrl)
		{
			var sKey;
			for (sKey in this.oKeys)
			{
				if (sKey.charCodeAt(0) === iKeyCode)
				{
					var fnListener = this.oKeys[sKey];
					if (fnListener) {
						fnListener.call(oEvent);
					}
					
					bEatKey = true;
					break;
				}
			}
			
			if (bEatKey) {
				oEvent.stop();
				return false;
			}
		}
		
		return true;
	}
};



/**
 * uiAjaxRequest is a wrapper for Prototype's Ajax.Request object,
 * which adds support for a xmlhttp timeout callback.
 * 
 * NOTE the onSuccess callback will still be called after the onTimeout() callback,
 * so always test "status" code 200 in the success callbacks (oAjaxResponse.status).
 * 
 * The timeout callback receives the same arguments as other Ajax.Request
 * callbacks : the Ajax.Response object.
 * 
 * To find out whether a timeout occured, the callback can use:
 * 
 *   oAjaxResponse.request.timeout     Indicates whether a timeout occured
 *
 * uiAjaxRequest is created just like Ajax.Request, and accepts
 * additional options:
 * 
 *   timeoutDelay     If specified, timeout delay in milliseconds, defaults to 5000 (5 seconds)
 *   onTimeout        Callback, must be specified
 * 
 */
var uiAjaxRequest = Class.create();
uiAjaxRequest.prototype =
{
	TIMEOUT_DELAY: 5000,

	initialize:function(url, options)
	{
		this.ajaxRequest = new Ajax.Request(url, options);
		
		this.ajaxRequest.timeout = false;
		this.timer = false;

		if (options.onTimeout)
		{
			// setup timeout handler
			options.timeoutDelay = options.timeoutDelay || this.TIMEOUT_DELAY;
			
			// patch up Prototype 1.6
			if (this.ajaxRequest.respondToReadyState)
			{
				var fn = this.ajaxRequest.respondToReadyState, that = this;
				
				this.ajaxRequest.respondToReadyState = function(readyState)
				{
					fn.apply(that.ajaxRequest, [readyState]);
					// onComplete
					if (readyState == 4)
					{
						that.clearTimeout();
					}
				}
			}
			else {
				throw new Error('uiAjaxRequest: Prototype function "respondToReadyState" could not be patched');
			}
			
			this.startTimer(options.timeoutDelay);
		}
	},

	startTimer:function(msec)
	{
		this.timer = setTimeout(this.handleTimeout.bind(this.ajaxRequest), msec);
	},

	handleTimeout:function()
	{
		//	console.log('this transport %o', this.transport);
		try {
			this.timeout = true;
			this.transport.abort();
			this.options.onTimeout(new Ajax.Response(this));
		}
		catch (e) {
			this.dispatchException(e);
		}
	},

	clearTimeout:function()
	{
		if (this.timer)
		{
			clearTimeout(this.timer);
			this.timer = false;
		}
	}
}


/**
 * uiAjaxIndicator displays a loading indicator in the top left corner of the
 * container element.
 * 
 * Options
 *   container       Parent element onto which the loading indicator is aligned.
 *                   If not set, the indicator appears at the top right of the page.
 *   message         Message to show in place of DEFAULT_MESSAGE, can contain html (eg. links)
 * 
 */
var uiAjaxIndicator = Class.create();
uiAjaxIndicator.prototype =
{
	DEFAULT_ZINDEX:    100,
	DEFAULT_MESSAGE:   'Loading...',
	
	initialize:function(options)
	{
		this.container = options && options.container ? options.container : document.body;
		this.message = options.message ? options.message : this.DEFAULT_MESSAGE;
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
		if (!this.indicator)
		{
			var pos = $(this.container).cumulativeOffset();

			this.indicator = new Element('span');
			this.indicator.setStyle(
			{
				padding:    '2px 10px',
				background: 'red',
				color:      '#fff',
				font:       '13px/18px Arial, sans-serif',
				position:   'absolute',
				left:		pos.left+'px',
				top:		pos.top+'px',
				zIndex:     this.DEFAULT_ZINDEX,
				display:	'block'
			})
			this.indicator.innerHTML = this.message;
			document.body.insertBefore(this.indicator, document.body.firstChild);
		}

		this.indicator.show();
	},
	
	hide:function()
	{
		if (this.indicator) {
			this.indicator.hide();
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
}


/**
 * uiAjaxPanel handles communication of content between client and server for a
 * portion of a html page.
 * 
 * Notes:
 * 
 * - During the Ajax communication, the portion of the page is covered with a
 *   layer that blocks mouse clicks. By default it is not visibile (fully transparent),
 *   but can be set to shading with option 'bUseShading'.
 * 
 * - Content cant be sent as a typical HTTP request or as JSON data.
 * 
 * - The response can be JSON or HTML.
 * 
 * By default the panel uses HTML requests. The server receives GET/POST requests
 * and returns HTML as for standard html pages, except no <head> or <body> tags
 * should be returned.
 * 
 * FORM submission:
 * 
 *   By default, the first FORM found in the panel will be serialized and sent via Ajax
 *   when it is submitted (onsubmit event). To use another FORM, set the "form" option.
 *   
 *   If a onSubmitForm listener was registered, then the listener must handle the form
 *   submission by calling get() or post() and specify what data to submit.
 *   
 *   If post_url is not set, then the action attribute of the FORM is used.
 *   
 *   If there is no FORM, ...todo.
 * 
 * @todo   If useJson is specified, the notification listerners can be used to post and
 *         respond to ajax requests and update the panel consequently.
 * 
 * Options:
 * 
 *   elContainer        Container element where content is loaded
 *   oOptions
 *     form             True by default, will pick the first FORM element in the panel.
 *                      To use another FORM element than the first one in the panel, specify a CSS rule (string)
 *                      to match the form to use (must be child of elContainer), or false to disable
 *                      the form submit binding.
 *     post_url         Url for requests, if not set will look for action attribute of a FORM element
 *     events           Handlers for notifications, object with notification name as properties and functions as values
 *     bUseLayer        Cover the area with a layer that blocks mouse clicks during ajax (defaults TRUE)
 *     bUseShading      If set and true, the container is darkened with a opacity layer while ajax is
 *     	                in process, otherwise a transparent layer is used (defaults FALSE).
 * 
 * Notifications:
 * 
 *   onContentInit()                 Called to initialize content of the panel
 *   onContentDestroy()				       Called before content is replaced with HTML ajax response
 *   onResponse(oAjaxResponse)       Ajax response received, BEFORE content is replaced (if response HTML)
 *                                   (oAjaxResponse is the prototype object)
 *   onFailure()					           Ajax error, AFTER the indicator shows the error/rety message.
 *   onSubmitForm(oEvent)            A form is submitted (oEvent is the Prototype event object). Use oEvent
 *                                   to identify the form element if needed (oEvent.element()).
 *   
 * Methods:
 *   post(parameters)                Submit via POST request, optional parameters (form, hash or query string)
 *   get(parameters)                 Submit via GET request, note that a request parameter '_q' is added that contains
 *                                   a unique value (unix time) to prevent browser from caching the response.
 *   connect()                       Call after post() to retry the last request
 * 
 * Ajax forms:
 * 
 *   If there is a FORM element, a notification will be thrown when the form is submitted.
 * 
 */
var uiAjaxPanel = Class.create();
uiAjaxPanel.prototype =
{
	initialize:function(elContainer, oOptions)
	{
	//	uiConsole.log('uiAjaxPanel.initialize()');

		oOptions = !!oOptions ? oOptions : {};

		// set defaults
		this.options = oOptions;
		this.options.form = (oOptions.form && oOptions.form !== true) ? oOptions.form : true;
		this.options.bUseLayer = oOptions.bUseLayer || true;
		this.options.bUseShading = oOptions.bUseShading || false;

		this.elContainer = elContainer;
		if (!this.elContainer)
			throw new Error("uiAjaxPanel.initialize() invalid container");

		this.evtCache = new uiEventCache();

		this.ajaxRequest = null;

		// register events
		this.eventDispatcher = new uiEventDispatcher();
		if (this.options.events)
		{
			for (var sEvent in this.options.events) {
				this.eventDispatcher.connect(sEvent, this.options.events[sEvent]);
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
	//	uiConsole.log('uiAjaxPanel.initContent()');
		var elForm = this.getForm();
		if (elForm) {
			this.initForm(elForm);
		}
		this.eventDispatcher.notify('onContentInit');
	},

	replaceContent:function(sHtml)
	{
	//	uiConsole.log('uiAjaxPanel.replaceContent()');

		this.evtCache.destroy();
		
		this.eventDispatcher.notify('onContentDestroy');
		
		this.elContainer.innerHTML = sHtml;

		// setup UI for forms, tables, etc.
		this.initContent();
	},

	/**
	 * Attach an event to FORMs that will dispatch a "onSubmit" event.
	 */
	initForm:function(elForm)
	{
		this.evtCache.addEvent(elForm, 'submit', this.submitFormEvent.bind(this));
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
			return this.elContainer.getElementsByTagName('form')[0];
		}
		else if (typeof(this.options.form === 'string'))
		{
			return this.elContainer.down(this.options.form);
		}
		
		return this.options.form;
	},

	/**
	 * 
	 * @param {Object} oEvent
	 */
	submitFormEvent:function(oEvent)
	{
	//	uiConsole.log('uiAjaxPanel.submitFormEvent(%o) Form %o', oEvent, oEvent.element());
		
		// if listener, let it handle the form, if no listeners, post FORM by default
		if (this.eventDispatcher.hasListeners('onSubmitForm'))
		{
			this.eventDispatcher.notify('onSubmitForm');
		}
		else
		{
			var elForm = this.getForm();
			this.post(elForm);
		}

		oEvent.stop();
	},


	/**
	 * Do a POST request with optional parameters in arguments or from a serialized form.
	 * 
	 * If oData is a FORM html element, the form data is serialized.
	 * 
	 * If a form is present and post_url was not set in options, the form action attribute is used.
	 * 
	 * @param {Object} oData    A FORM to serialize, a hash with post variables, or a query string
	 * @param {string} sMethod  Method name 'post' or 'get'
	 */
	post:function(oData)
	{
		this.prepareConnect(oData, 'post');
	},
	
	/**
	 * Do a GET request with optional parameters.
	 * 
	 * If oData is a FORM html element, the form data is serialized.
	 * 
	 * If a form is present and post_url was not set in options, the form action attribute is used.
	 * 
	 * @param {Object} sQuery   A query string or a hash of GET variables (OPTIONAL)
	 */
	get:function(oData)
	{
		var newDate = new Date();
		var sId = newDate.getTime();
		if (!arguments.length) {
			oData = {};
		}
		// add a unique value to prevent browser from caching GET request
		oData._q = newDate.getTime();
		this.prepareConnect(oData, 'get');
	},

	/**
	 * 
	 * @param {Object} oData    A FORM to serialize, a hash with post variables, or a query string
	 * @param {string} sMethod  Method name 'post' or 'get'
	 */
	prepareConnect:function(oData, sMethod)
	{
		var form, post_url;

		// optional parameters
		if (typeof(oData) === 'undefined') {
			oData = {};
		}
		
		// which form to serialize, if any
		if (oData.nodeName && oData.nodeName.toLowerCase()==='form')
		{
			form = oData;
			oData = Form.serialize(form, true);
		}
		else
		{
			form = this.getForm();
		}

		post_url = this.options.post_url || (form ? form.action : false);
		if (!post_url) {
			throw new Error('uiAjaxPanel.post() need post_url and/or a FORM element')
		}
		
		// dont send multiple requests at the same time
		if (this.ajaxRequest) {
			alert('Not so fast!');
			return;
		}

	//	uiConsole.log('uiAjaxPanel.prepareConnect(%o, %s) FORM %o', oData, sMethod, form);

		// start connection
		this.connect({
			url:        post_url,
			method:     sMethod,
			parameters: oData
		});
	},

	/**
	 * Establish the server connection with the current post() parameters.
	 * Call with arguments to establish the connection settings.
	 * Call with empty arguments to reconnect with the last settings, in case
	 * the connection failed or timed out.
	 * 
	 * Connection object:
	 *   url			  Url for Ajax.Request
	 *   method           As for Prototype Ajax.Request
	 *   parameters       As for Prototype Ajax.Request
	 */
	connect:function(oConnect)
	{
		if (oConnect) {
			this.connection = oConnect;
		}

		if (!this.connection) {
			throw new Error('uiAjaxPanel.connect() No connection object.');
		}

		this.ajaxRequest = new uiAjaxRequest(this.connection.url,
		{
			method:     this.connection.method,
			parameters: this.connection.parameters,

			// show/hide ajax loading indicator
			onCreate:	this.ajaxOnCreate.bind(this),
			onComplete:	this.ajaxOnComplete.bind(this),
			
			// response handlers
			onSuccess:  this.ajaxOnSuccess.bind(this),
			onFailure:  this.ajaxOnFailure.bind(this),
			onTimeout:  this.ajaxOnFailure.bind(this)
		});
	},

	ajaxOnCreate:function(oAjaxResponse)
	{
		// layer
		if (this.options.bUseLayer) {
			// create layer, then reuse it
			if (!this.shadeLayer) {
				this.shadeLayer = new uiShadeLayer({
					element:    this.elContainer,
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
		this.ajaxIndicator = new uiAjaxIndicator({container: this.elContainer});
		this.ajaxIndicator.show();
	},
	
	ajaxOnComplete:function(oAjaxResponse)
	{
	//	uiConsole.log('uiAjaxPanel.ajaxOnComplete()');
		
		// hide loading indicator
		this.ajaxIndicator.destroy();

		if (this.shadeLayer) {
			this.shadeLayer.hide();
		}
	},
	
	ajaxOnSuccess:function(oAjaxResponse)
	{
		// timeout will still trigger OnSuccess so check status
		if (oAjaxResponse.status!=200) {
			return false;
		}
		
		this.eventDispatcher.notify('onResponse', [oAjaxResponse]);
		if (oAjaxResponse.getHeader('Content-Type').indexOf('text/html')==0 &&
			oAjaxResponse.responseText.length)
		{
			this.replaceContent(oAjaxResponse.responseText);
		}

		this.ajaxRequest = null;
	},
	
	ajaxOnFailure:function(oAjaxResponse)
	{
	//	uiConsole.log('uiAjaxPanel.ajaxOnFailure(%o)', oAjaxResponse);
		
		if (oAjaxResponse.request.timeout)
		{
			// show the timeout message
			this.showErrorMessage('Oops! Timed out.');
			return;
		}

		var sMessage = 'Oops! Error '+oAjaxResponse.status+' "'+oAjaxResponse.statusText+'".';
		this.showErrorMessage(sMessage);

		this.ajaxRequest = null;
		this.eventDispatcher.notify('onFailure', [oAjaxResponse]);
	},
	
	/**
	 * Display a message in place of the ajax indicator,
	 * with a "Retry" link.
	 * 
	 * @param {Object} sMessage
	 */
	showErrorMessage:function(sMessage)
	{
		this.ajaxErrorIndicator = new uiAjaxIndicator({
			container: this.elContainer, 
			message: sMessage + ' <a href="#" style="font-weight:bold;color:yellow;">Retry</a>'
		});
		this.ajaxErrorIndicator.show();
		
		var elMessage = this.ajaxErrorIndicator.getElement();
		var elRetryLink = elMessage.getElementsByTagName('a')[0];
		$(elRetryLink).observe('click', this.ajaxRetryEvent.bind(this));
	},
	
	ajaxRetryEvent:function(oEvent)
	{
	//	uiConsole.log('uiAjaxPanel.ajaxRetryEvent()');
		this.ajaxErrorIndicator.destroy();
		this.connect();
	}
}


/**
 * uiShadeLayer creates a absolutely positioned div that covers an area of the page,
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
var uiShadeLayer = Class.create();
uiShadeLayer.prototype =
{
	/**
	 * Default settings
	 */
	DEFAULT_COLOR:	 '#000',
	DEFAULT_OPACITY: 20,
	
	/**
	 * Currently assumed to be lower value than uiAjaxIndicator DEFAULT_ZINDEX
	 */
	DEFAULT_ZINDEX:  90,
	
	initialize:function(options)
	{
		this.color = options.color || this.DEFAULT_COLOR;

		this.opacity = typeof(options.opacity)!=='undefined' ? options.opacity : this.DEFAULT_OPACITY;
		this.opacity = Math.max(Math.min(this.opacity, 100), 0);

	//unused
	//	var newDate = new Date();
	//	this.dom_id = newDate.getTime();

		// look for conditional comment div
		this.isIE = !!$('ie');

		if (options.element)
		{
			var offsets = $(options.element).cumulativeOffset();
			this.pos  = {
				left: offsets.left,
				top:  offsets.top
			}
			
			this.size = this.getDimensions(options.element);
			
			this.element = options.element;
		}
		else
		{
			this.pos  = options.pos;
			this.size = options.size;
		}

		var elLayer = document.createElement("div");
	//  elLayer.setAttribute('id', this.dom_id);
		var layerStyles = {
			display:  'none',
			position: 'absolute',
			top:      this.pos.top+'px',
			left:     this.pos.left+'px',
			zIndex:   this.DEFAULT_ZINDEX,
		 	width:    /%$/.test(this.size.width) ? this.size.width : this.size.width+'px',
			height:   /%$/.test(this.size.height) ? this.size.height : this.size.height+'px'
		};
		
		// set shading
		if (!options.glass_mode) {
	    	// ex: background:#85a5d2; opacity:0.5; filter:alpha(opacity=50);
			layerStyles.background = this.color;
		 	layerStyles.opacity = Math.round(this.opacity)/100;
		};

		$(elLayer).setStyle(layerStyles);
	 	if (this.isIE){
	 		// IE6 opacity filter
			elLayer.style.filter = 'alpha(opacity=' + this.opacity + ')';
		}

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
		this.elLayer.show();
	},
	
	hide:function()
	{
		this.elLayer.hide();
	},

	resize:function()
	{
		if (!this.element) {
			throw new Error('uiShadeLayer.resize() cannot resize without element');
		}
		
		var newSize = this.getDimensions(this.element);

		// try to fix small gap caused by Firefox
//		var elementSize = 

		if (newSize.width != this.size.width || newSize.height != this.size.height) 
		{
			this.size = newSize;
			//	uiConsole.log('resize to %o', this.size);
			$(this.elLayer).setStyle(
			{
				width: this.size.width + 'px',
				height: this.size.height + 'px'
			});
		}
	},
	
	visible:function()
	{
		return this.elLayer.visible();
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
		return element===document.body ? document.viewport.getDimensions() : $(element).getDimensions();
	}	
	
}


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
 */
var uiModalLayer = Class.create();
uiModalLayer.prototype =
{
	/**
	 * Currently between uiShadeLayer and uiAjaxIndicator's values
	 */
	DEFAULT_ZINDEX:  95,

	initialize:function(options)
	{
		// set defaults
		options.use_shading = !(options.use_shading===false);

		// detect IE with conditional comment DIV
		this.isIE = !!$('ie');

		this.layerDiv = options.element;

		this.evtCache = new uiEventCache('layer');
		this.layerSize = null;

		if (this.isIE) {
			this.comboBoxFixIE6(true, this.layerDiv);
		}

		this.shadeLayer = new uiShadeLayer( {
			element:    document.body,
			glass_mode: !options.use_shading
		} );
		this.shadeLayerDiv = $(this.shadeLayer.getLayer());
		this.shadeLayer.show();

		// center layer and make sure that the top and left values are not negative

		// get the dimensions without flashing display yet
		$(this.layerDiv).setStyle({ position:'absolute', left:'0', top:'0', visibility:'hidden', display:'block' });
		this.layerSize = {
			width:	this.layerDiv.offsetWidth,
			height:	this.layerDiv.offsetHeight
		};

		var arrayPageScroll = this.getPageScroll();
		var arrayPageSize = this.getPageSize();
		var layerTop = arrayPageScroll[1] + ((arrayPageSize[3] - this.layerSize.height) / 2);
		var layerLeft = ((arrayPageSize[2] - this.layerSize.width) / 2);

		$(this.layerDiv).setStyle({
			top:(layerTop < 0) ? "0px" : layerTop + "px",
			left:(layerLeft < 0) ? "0px" : layerLeft + "px",
			position:'absolute',
			zIndex: this.DEFAULT_ZINDEX,
			visibility: 'visible',
			display:'block'
		});

		// add event to prevent clicking through and triggering onclick events in other content while layer is on
		//2009/04/06 obsolete? the div eat clicks anyway ??
		//this.evtCache.addEvent(this.shadeLayerDiv, 'click', this.eatClicksYumYum.bindAsEventListener(this));
		//this.evtCache.addEvent(this.layerDiv, 'click', this.eatClicksYumYum.bindAsEventListener(this));

		// add event to reposition dialog
		this.evtCache.addEvent(window, 'resize', this.recenterLayer.bindAsEventListener(this));
		this.evtCache.addEvent(window, 'scroll', this.recenterLayer.bindAsEventListener(this));
	},

	destroy:function()
	{
		this.evtCache.destroy();
	
		// hide popup div
		this.layerDiv.style.display = 'none';

		// close the shade layer
		this.shadeLayer.destroy();

		if (this.isIE) {
			this.comboBoxFixIE6(false, this.layerDiv);
		}
	},
	
	recenterLayer:function()
	{
		// resize overlay and reposition dialog box
		var arrayPageSize = this.getPageSize();
		var arrayPageScroll = this.getPageScroll();

		// resize uiShadeLayer
		this.shadeLayer.resize();

		// center the popup div
		var layerLeft = ((arrayPageSize[2] - this.layerSize.width) / 2);
		var layerTop = arrayPageScroll[1] + ((arrayPageSize[3] - this.layerSize.height) / 2);
		$(this.layerDiv).setStyle({
			top:(layerTop < 0) ? "0px" : layerTop + "px",
			left:(layerLeft < 0) ? "0px" : layerLeft + "px"
		});
	},
	
	/* helper functions */

	/*
	eatClicksYumYum:function(e)
	{
		var elem = Event.element(e);
		var elemType = elem.nodeName.toLowerCase();

		// let event pass through for input elements
		switch(elemType){
			case 'input':
			case 'select':
				return true;
		}

		// eat clicks outside of the layer while the layer is on
		// (assumes that the "shade" layer is covering the whole visible area)

		Event.stop(e);
		return false;
	},
	*/

	// Returns array with x,y page scroll values (core code from - quirksmode.org)
	getPageScroll:function()
	{
		var xScroll, yScroll;

		if (typeof(self.pageYOffset)!=='undefined')
		{
			xScroll = self.pageXOffset;
			yScroll = self.pageYOffset;
		}
		else if (document.documentElement && typeof(document.documentElement.scrollTop)!=='undefined')
		{	// Explorer 6 Strict
			xScroll = document.documentElement.scrollLeft;
			yScroll = document.documentElement.scrollTop;
		}
		else if (document.body)
		{	// all other Explorers
			xScroll = document.body.scrollLeft;
			yScroll = document.body.scrollTop;
		}

		var arrayPageScroll = new Array(xScroll,yScroll) 
		return arrayPageScroll;
	},
	
	// Returns array with page width, height and window width, height (core code from - quirksmode.org)
	getPageSize:function()
	{
		var xScroll, yScroll;
		var windowWidth, windowHeight;
		var pageWidth, pageHeight;
		
		if (window.innerHeight && window.scrollMaxY) {	
			xScroll = window.innerWidth + window.scrollMaxX; //document.body.scrollWidth;
			yScroll = window.innerHeight + window.scrollMaxY;
		} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}

		if (self.innerHeight) {	// all except Explorer
			windowWidth = self.innerWidth;
			windowHeight = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
			windowWidth = document.documentElement.clientWidth;
			windowHeight = document.documentElement.clientHeight;
		} else if (document.body) { // other Explorers
			windowWidth = document.body.clientWidth;
			windowHeight = document.body.clientHeight;
		}	

		// for small pages with total height less then height of the viewport
		if (yScroll < windowHeight){
			pageHeight = windowHeight;
		} else {
			pageHeight = yScroll;
		}

		// for small pages with total width less then width of the viewport
		if (xScroll < windowWidth){
			pageWidth = windowWidth;
		} else {
			pageWidth = xScroll;
		}

		var arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
		return arrayPageSize;
	},
	
	// Hide <select> elements while layer is active, to prevent display bug in IE<=6
	//  bEnableFix  true to start (hides elements), false to end (restore element visibility)
	//  layerDiv    selects inside DIV will not be affected
	comboBoxFixIE6:function(bEnableFix, layerDiv)
	{
		var elems = $A(document.getElementsByTagName('select'));

		// return true if value is in array
		function in_array(pArray, value)
		{
			for(var i=0; i<pArray.length; i++)
			{
				if (pArray[i]===value)
					return true;
			}
			return false;
		}

		if (bEnableFix)
		{
			var pExcludeSelects = $A(layerDiv.getElementsByTagName('select'));
			for (i=0; i<elems.length; i++)
			{
				if (!in_array(pExcludeSelects, elems[i]))
				{
					$(elems[i]).addClassName('IE6ComboBoxFix');
				}
			}
		}
		else
		{
			for (i=0; i<elems.length; i++)
			{
				$(elems[i]).removeClassName('IE6ComboBoxFix');
			}
		}
	}
}


/**
 * uiModalDialog creates custom dialogs with uiModalLayer, and automatic
 * binding of buttons/links to event handlers. It provides a default
 * handler to close the dialog for simple "ok" messages.
 * 
 * Dialog buttons should use the A tag and a classname that indicates the
 * callback function to use. The default callback is "closeDialog", this
 * button closes the dialog:
 * 
 *   <a href="#" class="JsButton-closeDialog">Cancel</a>
 * 
 * To add a custom action, add the function to the dialog instance:
 * 
 *   var dlg = new uiModalDialog(div);
 *   dlg.myAction = function() {
 *      // do something, then close dialog with destroy()
 *      this.destroy();
 *   }
 *   
 *   <a href="#" class="JsButton-myAction">Do It!</a>
 * 
 * @uses  uiLayer, uiEventCache
 */
var uiModalDialog = Class.create();
uiModalDialog.prototype =
{
	initialize:function(dialogDivId)
	{
		uiConsole.log('uiModalDialog.initialize()');

		this.evtCache = new uiEventCache('uiModalDialog');

		var dialogDiv = $(dialogDivId);
		if (!dialogDiv){
			alert('uiModalDialog.initialize() - dialog DIV "'+dialogDivId+'" not found.');
		}

		// initialize button events
		var links = dialogDiv.getElementsByTagName('a');
		for (var i=0; i<links.length; i++) {
			this.evtCache.addEvent(links[i], 'click', this.buttonHandler.bindAsEventListener(this));
		}

		this.layer = new uiModalLayer({	element: dialogDiv });
	},

	destroy:function()
	{
		this.evtCache.destroy();
		this.layer.destroy();
	},

	buttonHandler:function(e)
	{
		var elem = Event.findElement(e, 'a');

		if (/JsButton-(\w+)/.test(elem.className))
		{
			var fnId = RegExp.$1;
			if (fnId==='closeDialog'){
				this.destroy();
			}
			else {
				if (typeof(this[fnId])!=='undefined')
				{
					if (this[fnId]())
					{
						this.destroy();
					}
				}
				else {
					alert('uiModalDialog.buttonHandler() - function '+fnId+' not defined');
				}
				this.destroy();
			}
		}
		Event.stop(e);
		return false;
	},
	
	// default button, maps to class "JsButton-closeDialog" in the dialog button (html)
	closeDialog:function()
	{
		this.destroy();
	}
}


/**
 * uiPopupLayer - Create a absolutely positioned layer which is aligned to a chosen element of
 * the document. Most useful for creating "tooltips".
 *
 * By default the popup shows under the element (alignBottom), and is aligned to the left (alignLeft).
 *
 * Options:
 *
 *   width         If specified, set width, otherwise uses default rendering from content & CSS
 *   height        If specified, set height, ...
 *   
 *   hook		       Alignment of the tooltip in relation to the target, <top|middle|bottom><left|center|right>
 *   
 *   offsetLeft    Relative offset after alignment, positive = move right, negative move left
 *   offsetTop     Relative offset after alignment, positive = move up, negative move down
 *   
 *   use_hover	   True to attach mouse hover events (default false)
 *
 *  
 * @see      Inspiration  http://www.nickstakenburg.com/projects/prototip2/
 */
uiPopupLayer = Class.create();
uiPopupLayer.prototype =
{
	DEFAULT_ZINDEX:  100,
	
	DEFAULT_STYLE: 'background:#fff; border:1px solid #888; padding:5px; font:12px/1.4em Verdana, sans-serif;',

	/**
	 * Constructor
	 *
	 * @param  HTMLElement  element   HTML element to align popup with
	 * @param  HTMLElement  content   Another element to show as popup, or a string for dynamic popup (innerHtml)
	 * @param  Object       options   See documentation
	 */
	initialize:function(element, content, options)
	{
		var sOptionHook;

		uiConsole.log('uiPopupLayer.initialize(%o)', element);
	
		// set options
		this.elAttach = element;
		this.content = content;
		this.options = options ? options : {};


		// Set defaults
		this.isVisible = false;
		this.options.hook = this.options.hook || 'topCenter';
		sOptionHook = this.options.hook.toLowerCase();

		if (/(top|middle|bottom)(left|center|right)/.test(sOptionHook))
		{
			this.options.align = { h: RegExp.$2, v:RegExp.$1 };
		}
		else
		{
			this.options.align = { h: 'center', v: 'top' };
		}
		
		if (sOptionHook=='middleleft' || sOptionHook=='middleright') {
			this.options.align.h = sOptionHook;
		}

		this.options.offsetLeft = this.options.offsetLeft ? this.options.offsetLeft : 0;
		this.options.offsetTop = this.options.offsetTop ? this.options.offsetTop : 0;

		this.isDynamic = typeof(content)==='string';
		this.elPopup  = this.isDynamic ? false : $(content);
		
		if (!this.elPopup)
		{
			// create the popup elements
			this.elPopup = new Element('div');
			this.elPopup.innerHTML = '<div style="'+this.DEFAULT_STYLE+'">'+this.content+'</div>' || '&nbsp;';
			this.elPopup.setStyle({
				display:  'none',
				position: 'absolute',
				zIndex:   this.DEFAULT_ZINDEX
			});
			
			// insert into document
			document.body.insertBefore(this.elPopup, document.body.firstChild);
		}
		else
		{
			this.elPopup.setStyle({
				display:  'none',
				position: 'absolute',
				zIndex:   this.DEFAULT_ZINDEX
			});
		}
		
		if (this.options.use_hover)
		{
			// hover mode
			this.evtCache = new uiEventCache();
			this.evtCache.addEvent(this.elAttach, 'mouseover', this.evMouseHover.bindAsEventListener(this));
			this.evtCache.addEvent(this.elAttach, 'mouseout', this.evMouseHover.bindAsEventListener(this));
		}
	},

	destroy:function()
	{
		uiConsole.log('uiPopupLayer.destroy()');

		if (this.evtCache) {
			this.evtCache.destroy();
		}

		this.toggleDisplay(false);

		// remove element from document
		if (this.isDynamic && this.elPopup.parentNode) {
			document.body.removeChild(this.elPopup);
		}
	},

	evMouseHover:function(oEvent)
	{
		// mouseenter/mouseleave behaviour
		var element = this.elAttach;
		var parent = oEvent.relatedTarget || null;
        while (parent && parent != element) {
        	try { parent = parent.parentNode; }
        	catch(e) { parent = element; }
        }
		if (parent == element) {
			oEvent.stop();
			return;
		}

		switch (oEvent.type)
		{
			case 'mouseover':
				this.show();
				break;
			case 'mouseout':
				this.hide();
				break;
			default:
				break;
		}

		oEvent.stop();
	},

	show:function()
	{
		if (this.fnAutoClose) {
			clearTimeout(this.fnAutoClose);
			this.fnAutoClose = null;
		}

		this.toggleDisplay(true);
	},

	hide:function()
	{
		this.toggleDisplay(false);
	},
	
	toggleDisplay:function(bVisible)
	{
		var oLayer = this.elPopup;

		if (bVisible)
		{
			// Position of the attach element to set layer position relative to
			var basePos  = $(this.elAttach).cumulativeOffset();
			var layerPos = { left:0, top:0 };
			var offsets = {
				left: this.options.offsetLeft ? this.options.offsetLeft : 0,
				top:  this.options.offsetTop ? this.options.offsetTop : 0
			};
			
			// Dimensions of the popup layer needed to compute alignments
			var aSize;
			
			// do we need to find out the popup layer dimensions?
			if (!this.options.width || !this.options.height)
			{
				// Prototype can guess the dimensions of a hidden element
				aSize = this.elPopup.getDimensions();
			}
			
			// enforce wdith or height if specified
			aSize.width  = this.options.width ? this.options.width : aSize.width;
			aSize.height = this.options.height ? this.options.height : aSize.height;

			// horizontal alignment
			switch (this.options.align.h) {
				case 'middleleft':
					offsets.left = offsets.left - aSize.width - 1; break;
				case 'middleright':
					offsets.left = offsets.left + this.elAttach.offsetWidth + 1; break;
				case 'left':
					break;
				case 'right':
					offsets.left = offsets.left - aSize.width + this.elAttach.offsetWidth; break;
				case 'center':
				default:
					offsets.left = Math.floor(offsets.left + (this.elAttach.offsetWidth/2) - (aSize.width/2)); break;
			}

			// vertical alignment
			switch (this.options.align.v) {
				case 'top':
					offsets.top = offsets.top - 1 - aSize.height; break;
				case 'bottom':
					offsets.top = offsets.top + this.elAttach.offsetHeight + 1; break;
				case 'middle':
				default:
					offsets.top = Math.floor(offsets.top + (this.elAttach.offsetHeight/2) - (aSize.height/2)); break;
			}

			// set styles
			var styles =
			{
				display:    'block',
				left:       basePos[0] + offsets.left + 'px',
				top:        basePos[1] + offsets.top + 'px'
			};

			// explicitly set the dimensions if specified by options
			if (this.options.width) {
				styles.width = this.options.width + 'px';
			}
			if (this.options.height) {
				styles.height = this.options.height + 'px';
			}
			
			oLayer.setStyle(styles);
		}
		else {
			oLayer.setStyle({
				display: 'none'
			});
		}
	}
}
