/**
 * toolbox.js
 * 
 * Includes small subset of functions from Prototype JavaScript framework (http://www.prototypejs.org/)
 *  (c) 2005-2007 Sam Stephenson
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

var Class = {
  create: function() {
    return function() {
      this.initialize.apply(this, arguments);
    }
  }
}

Object.extend = function(destination, source) {
  for (var property in source) {
    destination[property] = source[property];
  }
  return destination;
}

Function.prototype.bind = function() {
  var __method = this, args = $A(arguments), object = args.shift();
  return function() {
    return __method.apply(object, args.concat($A(arguments)));
  }
}

Function.prototype.bindAsEventListener = function() {
  var __method = this, args = $A(arguments), object = args.shift();
  return function(event) {
    return __method.apply(object, [event || window.event].concat(args));
  }
}

var $A = Array.from = function(iterable) {
  if (!iterable) return [];
  if (iterable.toArray) {
    return iterable.toArray();
  } else {
    var results = [];
    for (var i = 0, length = iterable.length; i < length; i++)
      results.push(iterable[i]);
    return results;
  }
}

var Enumerable = {
  each: function(iterator) {
    var index = 0;
    try {
      this._each(function(value) {
        iterator(value, index++);
      });
    } catch (e) {
      if (e != $break) throw e;
    }
    return this;
  }
}  

Object.extend(Array.prototype, Enumerable);
Object.extend(Array.prototype, {
  _each: function(iterator) {
    for (var i = 0, length = this.length; i < length; i++)
      iterator(this[i]);
  }
});


function $(element) {
  if (arguments.length > 1) {
    for (var i = 0, elements = [], length = arguments.length; i < length; i++)
      elements.push($(arguments[i]));
    return elements;
  }
  if (typeof element == 'string')
    element = document.getElementById(element);
  return element;/*Element.extend(element);*/
}

if (!window.Event) {
  var Event = new Object();
}
Object.extend(Event, {
  KEY_BACKSPACE: 8,
  KEY_TAB:       9,
  KEY_RETURN:   13,
  KEY_ESC:      27,
  KEY_LEFT:     37,
  KEY_UP:       38,
  KEY_RIGHT:    39,
  KEY_DOWN:     40,
  KEY_DELETE:   46,
  KEY_HOME:     36,
  KEY_END:      35,
  KEY_PAGEUP:   33,
  KEY_PAGEDOWN: 34,

  element: function(event) {
    return $(event.target || event.srcElement);
  },
  
  stop: function(event) {
    if (event.preventDefault) {
      event.preventDefault();
      event.stopPropagation();
    } else {
      event.returnValue = false;
      event.cancelBubble = true;
    }
  }

});


/* dom utility */

var dom = {

	// nodetypes
	ELEMENT_NODE:1,	ATTRIBUTE_NODE:2, TEXT_NODE:3,
	
	walkTheDOM:function(node, func)
	{
		func(node);
		node = node.firstChild;
		while (node) {
			dom.walkTheDOM(node, func);
			node = node.nextSibling;
		} 
	},

	// crossbrowser addEvent
	addEvent: function(obj, evType, fn, useCapture)
	{
		if (useCapture==undefined)
			useCapture = false; // use bubbling
		if (obj.addEventListener){
			obj.addEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.attachEvent){
			var r = obj.attachEvent("on"+evType, fn);
			return r;
		} else {
			alert("Handler could not be attached");
		}
	},
	
	// crossbrowser removeEvent
	removeEvent: function(obj, evType, fn, useCapture)
	{
		if (useCapture==undefined)
			useCapture = false; // use bubbling
		if (obj.removeEventListener){
			obj.removeEventListener(evType, fn, useCapture);
			return true;
		} else if (obj.detachEvent){
			var r = obj.detachEvent("on"+evType, fn);
			return r;
		} else {
			alert("Handler could not be removed");
		}
	},

	purgeEventHandlers: function(node)
	{
		dom.walkTheDOM(node, function (e) {
			for (var n in e) {            
				if (typeof e[n] === 'function') {
					e[n] = null;
				}
			}
		});
	},

	/* assign an eventhandler to a list of eventtypes for given element */
	delegateEvents: function(element, eventtypes, eventhandler)
	{
		for (var i=0;i<eventtypes.length; i++) {
			element['on'+eventtypes[i]] = eventhandler;
		}
	},

	/* Copyright Robert Nyman, http://www.robertnyman.com */
	getElementsByClassName: function (oElm, strTagName, strClassName)
	{
	    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	    var arrReturnElements = new Array();
	    strClassName = strClassName.replace(/\-/g, "\\-");
	    var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	    var oElement;
	    for(var i=0; i<arrElements.length; i++){
	        oElement = arrElements[i];      
	        if(oRegExp.test(oElement.className)){
	            arrReturnElements.push(oElement);
	        }
	    }
	    return (arrReturnElements);
	},

	/* Copyright Robert Nyman, http://www.robertnyman.com */
	getElementsByAttribute: function(oElm, strTagName, strAttributeName, strAttributeValue)
	{
	    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	    var arrReturnElements = new Array();
	    var oAttributeValue = (typeof strAttributeValue != "undefined")? new RegExp("(^|\\s)" + strAttributeValue + "(\\s|$)") : null;
	    var oCurrent;
	    var oAttribute;
	    for(var i=0; i<arrElements.length; i++){
	        oCurrent = arrElements[i];
	        oAttribute = oCurrent.getAttribute && oCurrent.getAttribute(strAttributeName);
	        if(typeof oAttribute == "string" && oAttribute.length > 0){
	            if(typeof strAttributeValue == "undefined" || (oAttributeValue && oAttributeValue.test(oAttribute))){
	                arrReturnElements.push(oCurrent);
	            }
	        }
	    }
	    return arrReturnElements;
	},

	// returns parent html element of given tag, or null if not found, optional classname
	getParent: function(el, sTagName, classname)
	{
		function recursive(el, sTagName, classname) {
			if (el == null)
				return null;
			// gecko bug, supposed to be uppercase
			else if (el.nodeType==dom.ELEMENT_NODE && el.tagName.toLowerCase()==sTagName)
			{
				var oClassValue = classname ? new RegExp("(^|\\s)" + classname + "(\\s|$)") : null;
				if (oClassValue===null || oClassValue.test(el.className))
					return el;
			}
			return recursive(el.parentNode, sTagName, classname);
		}
		return recursive(el, sTagName, classname);
	},

	/* by Peter-Paul Koch http://www.quirksmode.org */
	findPosition: function(obj)
	{
		var curleft = 0;
		var curtop = 0;
		if (obj.offsetParent)
		{
			curleft = obj.offsetLeft;
			curtop = obj.offsetTop;
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			}
		}
		return [curleft, curtop];
	},
	
	// set element styles shorthand
	setStyle:function(element, oStyles)
	{
		for (var property in oStyles) {
			element.style[property] = oStyles[property];
		}
	}
}


/* css */

var CssClass = {
	classnames: function(element) {
		return element.className.split(/\s+/);
	},
	
	set: function(element, s) {
		element.className = s;
	},
	
	// adds a class taking care of whether it was present already or not
	add: function(element, s) {
		if (this.has(element,s)) return;// minimize refresh (for buggy IE)
		
		var classes = this.classnames(element);
		for (var i=0;i<classes.length;i++) {
			if (classes[i]=='s') return;
		}
		var newclasses = classes.concat(s).join(' ');
		element.className = newclasses;
	},
	
	has: function(element, s) {
		var classes = this.classnames(element);
		for (var i=0;i<classes.length;i++) {
			if (classes[i]==s) {
				return true;
			}
		}
		return false;
	},

	remove: function(element, s) {
		if (!this.has(element,s)) return; // minimize refresh (for buggy IE)
		
		var classes = this.classnames(element);
		var newclasses = '';
		for (var i=0;i<classes.length;i++) {
			if (classes[i]!=s) {
				newclasses += (i>0?' ':'')+classes[i];
			}
		}
		element.className = newclasses;
	},

	replace: function(element, sold, snew) {
		var classes = this.classnames(element);
		var newclasses = '';
		for (var i=0;i<classes.length;i++) {
			if (classes[i]!=sold) {
				newclasses += (i>0?' ':'')+classes[i];
			}
		}
		element.className = newclasses+' '+snew;
	}
}

/* dhtml helpers */

var dynHTML =
{
	clearTableBody:function(t)
	{
		while(t.rows.length){
			t.deleteRow(0);
		}
		return;
	},
	
	insertTableRow:function(tbody, oRow, iRowIndex)
	{
	//	console.log('dynHTML::insertTableRow() tbody=%o oRow=%o iRowIndex=%d',tbody,oRow,iRowIndex);
		var tr, td, cells = oRow.tCells;

		// insert at end of table by default
		if (iRowIndex===undefined)
			iRowIndex = -1;

		if (!cells)
			return;

		tr = tbody.insertRow(iRowIndex);

		if (oRow.tId)
			tr.id = oRow.tId;
		if (oRow.tClass)
			tr.className = oRow.tClass;

		for (var i=0; i<cells.length; i++)
		{
			td = tr.insertCell(-1);
			if (cells[i].tClass)
				td.className = cells[i].tClass;
			td.innerHTML = cells[i].tHtml;
		}
	},

	/* input handling :	just thrown here in dynHTML for now */

	// cross-browser move caret to end of input field
	setCaretToEnd:function(control)
	{
		if (control.createTextRange) {
			var range = control.createTextRange();
			range.collapse(false);
			range.select();
		}
		else if (control.setSelectionRange) {
			control.focus();
			var length = control.value.length;
			control.setSelectionRange(length, length);
		}
	}
}

/* ajax */

// use:	var myXmlHttp = new XmlHttpObject();
//
function XmlHttpObject()
{
	var xmlhttp = false;
	var responseHandler = null;

	this.bBusy = false;
	this.bDebug = false;

	/*@cc_on
	@if (@_jscript_version >= 5)
	try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
	  try {
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	    xmlhttp = false;
	  }
	}
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		try {
		// native XMLHttpRequest object ?
			xmlhttp = new XMLHttpRequest();
		} catch (e) {
			xmlhttp = false;
		}
	}
	if (!xmlhttp && window.createRequest) {
		try {
		// IceBrowser ?
			xmlhttp = window.createRequest();
		} catch (e) {
			xmlhttp=false;
		}
	}

	if (!xmlhttp) {
		return null;
	}
	
	this.connect = function(sMethod, sURL, sVars, funcHandleResponse)
	{
		if (!xmlhttp)
			return false;
	
		responseHandler = funcHandleResponse;
		
		this.bBusy = true;
		
		try {
			if (sMethod == "GET")
			{
				xmlhttp.open(sMethod, sURL+"?"+sVars, true);
				sVars = "";
			}
			else
			{
				xmlhttp.open(sMethod, sURL, true);
				xmlhttp.setRequestHeader("Method", "POST "+sURL+" HTTP/1.1");
				xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			
			xmlhttp.onreadystatechange = this.onReadyState.bind(this);
			xmlhttp.send(sVars);
		}
		catch(z) { return false; }
		return true;
	},

	this.onReadyState = function()
	{
		if (xmlhttp.readyState == 4 && this.bBusy)
		{
			this.bBusy = false;

			if (xmlhttp.status==200)
			{
		        var response = xmlhttp.responseText;
		
				if (this.bDebug) {
					// show response
		        	//document.getElementById('response').innerHTML = '<div class="actionconfirmationmessage">'+response+'</div>';
		        	alert('response = '+response);
		    	}
		    	
		    	responseHandler(response);
			}
			else
			{
				alert('xmlhttp object status error: '+xmlhttp.status);
			}
		}
	}
}


/* common code, unsorted */

var App = {
	// php will pass variables here (<script ...>App.loaddata.xxx = ...</script>)
	loaddata:{},

	debugresponse:function(response)
	{
		if (response.indexOf('@')==0) {
			var objResponse = response.substr(1).evalJSON(true);
			if (objResponse.dbgtime){
				var dbgtimeDiv = $('dbgtime');
				dbgtimeDiv.innerHTML = 'Generated in '+objResponse.dbgtime+' seconds';
			}
			return objResponse;
		}
		
		var debugDiv = $('debugresponse');
		if (!debugDiv){
			// create the debugging div
			debugDiv = document.createElement('div');
			debugDiv.id = 'debugresponse';
			dom.setStyle(debugDiv,{
				position:'absolute',
				right:'1px',
				top:'1px',
				width:'auto'
			})
			document.getElementsByTagName('body')[0].appendChild(debugDiv);
		}
		
		// if '@' is not present, it will be a server-side error message/debugging output
		debugDiv.style.display = 'block';
    	debugDiv.innerHTML = '<p>'+response+'</p>';
		
		return null;
	}
};


/* user interface */

var AppUI = {

	// this is for behaviours on all pages!
	initialize:function()
	{
		// Initialize help system link, if any
		var helpLink = $('DynHelp');
		if (helpLink){
			helpLink.onclick = this.helpSystemEvent.bindAsEventListener(this, helpLink);
		}
	},
	
	// dynamically load the help-system class, and the help data for the current page
	helpSystemEvent:function(e, helpLink)
	{	
		function loadScript(url)
		{
			// dynamically load javascript file
			var scriptElem = document.createElement('script');
			scriptElem.src = url;
			scriptElem.type = 'text/javascript';
			document.getElementsByTagName('head')[0].appendChild(scriptElem);
		}
		
		if (!this.dynHelpSystem)
		{
			// load help system and this page's help
			var pageId = /DynHelpId-([^\s]+)/.test(helpLink.className) ? RegExp.$1 : null;
			if (pageId){
				loadScript('/vocab/help/help.php?page='+pageId);
			}
		}
		else
		{
			this.dynHelpSystem.start();
		}
		
		Event.stop(e);
		return false;
	},

	/* */
	toggleDisplay: function(id){
		var element = $(id);
		element.style.display = (element.style.display == 'none') ? '' : 'none';
	},

	/* sets html element's alpha transparency level, opacity is a float between 0 and 1 */
	setOpacity:function(div, opacity)
	{
		var itsIE = !!$('ie');
		if (itsIE){
			div.style.filter = 'alpha(opacity='+Math.ceil(opacity*100)+')';
		}
		else{
			div.style.opacity = opacity.toFixed(2);
		}
	},

	/* */
	scrollTop: function(){
		var dx = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0;
		window.scrollTo(dx,0);
	},
	
	/* cross-browser css :hover pseudo-class */
	addHoverState:function(element){
		if (typeof element == 'string')
			element = $(element);
		dom.addEvent(element, 'mouseover', this.eventElementHoverOver.bindAsEventListener(this) );
		dom.addEvent(element, 'mouseout', this.eventElementHoverOut.bindAsEventListener(this) );
	},
	eventElementHoverOver:function(e){
		var element = Event.element(e);
		CssClass.add(element,'hover');
	},
	eventElementHoverOut:function(e){
		var element = Event.element(e);
		CssClass.remove(element,'hover');
	},
	
	/* re-usable events */
	selectAllOnFocus:function(e){
		this.select();
	}
}

/* ui stuff */


/* event cache & clean for IE memory leaks */

dom.getHiddenParams = function(div)
{
	var i, msgs = div.getElementsByTagName('input');
	var params = { };
	for (i=0; i<msgs.length; i++)
	{
		if (/Js(Data|Form)-(\w+)/.test(msgs[i].className))
		{
			var sType = RegExp.$1;
			var sPropName = RegExp.$2 || alert('dom::getHiddenParams() - Empty property name');
			if (sType==='Data'){
				params[sPropName] = msgs[i].value;
			}
			else {
				if (!params.formData) {
					params.formData = {};
				}
				params.formData[sPropName] = msgs[i].value;
			}
		}
	}
	return params;
};


var EventCache = Class.create();
EventCache.prototype = {
	initialize:function(sId) {
		this.sId = sId;
		this.eCache = [];
	},

	push:function(element, sEventType) {
		this.eCache.push({oElem:element, evType:sEventType});
	},
	
	addEvent:function(element, sEventType, fn)
	{
		element['on'+sEventType] = fn;
		this.push(element, sEventType);
	},

	purge:function() {
		if (this.eCache)
		{
			//console.log('EventCache::purge('+this.sId+') '+this.eCache.length+' events');
			for(var i=this.eCache.length-1; i>=0; i--){
				var evc = this.eCache[i];
				evc.oElem['on'+evc.evType] = null;
			}
			// free up references to the elements
			this.eCache =[];
		}
	}
}


/* uiTable :: enhanced table class with row highlighting */

var uiTable = Class.create();
uiTable.prototype = {
	
	DATAROW_HIGHLIGHT_CLASS: 'hover',		// class applied to table row on hover

	// add mouseover highlight to rows in tables of class "cstp-tabular"
	initialize: function(table)
	{
		//console.log('uiTable::initialize(%o)', table);
		this.evtCache = new EventCache('uiTable');
		var tbody = table.tBodies[0];
		if (tbody){
			dom.delegateEvents(tbody, ["mouseover","mouseout"], this.eventHandler.bindAsEventListener(this));
			this.evtCache.push(tbody, 'mouseover');
			this.evtCache.push(tbody, 'mouseout');
		}
	},
	
	eventHandler:function(e)
	{
		var elem = Event.element(e);
		var row = dom.getParent(elem,'tr');
		var evType = e.type;
		switch(evType){
			case "mouseover": CssClass.add(row, this.DATAROW_HIGHLIGHT_CLASS); break;
			case "mouseout": CssClass.remove(row, this.DATAROW_HIGHLIGHT_CLASS); break;
		}		
	},
	
	destroy:function()
	{
		this.evtCache.purge();
	}
}


var DBG_CONSOLE_ENABLED = false;
var DBG_CONSOLE_ID = 'koohii_console';

// FreBug console debugging ( http://www.joehewitt.com/software/firebug/ )
// If not present (IE/Opera/Safari), dynamically create a div to output messages.
var DBGConsole = {
	debugCounter: 0,
	
	log: function(message)
	{
		if (!DBG_CONSOLE_ENABLED) return;
	    this.debugCounter++;
	
		var found = !!document.getElementById(DBG_CONSOLE_ID);
		if (!found) {
			// create the debugging div
			var d = document.createElement('div');
			d.id = DBG_CONSOLE_ID;
			d.style.position = 'absolute';
			d.style.right = '0';
			d.style.top = '0';
			d.style.width = '300px';
			d.style.border = '2px solid #dc0000';
			d.style.background = '#fff5f5';
			d.style.padding = '5px';
			d.style.font = 'bold 10px Verdana;';
			d.style.textAlign = 'left';
			d.style.color = '#000';
			d.style.font = '12px Courier New, monospace;';
		}
		var t = document.createTextNode(''+this.debugCounter + ': ' + message);
	    var br = document.createElement('br');
		d.appendChild(t);
	    d.appendChild(br);
		if (!found) {
			document.getElementsByTagName('body')[0].appendChild(d);
		}
	},
	profile:function(){},
	profileEnd:function(){},
	time:function(sTimerId){
		this.timerName = sTimerId!==undefined ? sTimerId : '';
		this.startTime = new Date().getTime();
	},
	timeEnd:function(){
		this.endTime = new Date().getTime();
		this.log('profileEnd('+this.timerName+') :: '+(this.endTime-this.startTime)+' msec');
	}
}

if (!console && DBG_CONSOLE_ENABLED)
{
	var console = DBGConsole;
}


dom.addEvent(window,'load',function(){ AppUI.initialize(); });
